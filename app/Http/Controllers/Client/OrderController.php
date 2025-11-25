<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServicePackage;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Show order form with available service packages
     */
    public function create()
    {
        $packages = ServicePackage::where('is_active', true)
            ->orderBy('base_price', 'asc')
            ->get();
            
        return view('client.orders.create', compact('packages'));
    }

    /**
     * Store new order and create invoice
     */
    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:service_packages,id',
            'domain' => 'required|string|max:255|unique:services,domain',
            'billing_cycle' => 'required|in:monthly,annually',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
        $package = ServicePackage::findOrFail($request->package_id);
        
        // Calculate price (gunakan base_price langsung, tanpa dikali 12)
        $price = $package->base_price;
        
        // Ensure price is not null
        if (is_null($price)) {
            $price = 0;
        }

        try {
            DB::beginTransaction();

            // Create service with pending status
            $service = Service::create([
                'client_id' => $user->id,
                'package_id' => $package->id,
                'product' => $package->name,
                'domain' => $request->domain,
                'price' => $price,
                'billing_cycle' => $request->billing_cycle,
                'registration_date' => now(),
                'status' => 'Pending',
                'due_date' => now()->addDays(7), // Due in 7 days for first payment
                'notes' => $request->notes,
            ]);

            // Generate invoice number
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            
            // Create invoice using DB::table to bypass model issues
            $invoiceId = DB::table('invoices')->insertGetId([
                'number' => $invoiceNumber,
                'title' => "Order: {$package->name}",
                'client_id' => $user->id,
                'service_id' => $service->id,
                'subtotal' => (float) $price,
                'total_amount' => (float) $price,
                'status' => 'Unpaid',
                'due_date' => now()->addDays(7),
                'description' => "Order: {$package->name} - {$request->domain} ({$request->billing_cycle})",
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Get the invoice for redirect
            $invoice = Invoice::find($invoiceId);

            DB::commit();

            return redirect()->route('client.orders.success', $invoice->id)
                ->with('success', 'Order berhasil dibuat! Silakan lakukan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat order: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show order success page with invoice
     */
    public function success($invoiceId)
    {
        $invoice = Invoice::with(['service'])->findOrFail($invoiceId);
        
        // Ensure this invoice belongs to current user
        if ($invoice->client_id !== auth()->id()) {
            abort(403);
        }

        return view('client.orders.success', compact('invoice'));
    }

    /**
     * Get package details via AJAX
     */
    public function getPackageDetails($id)
    {
        $package = ServicePackage::findOrFail($id);
        
        return response()->json([
            'id' => $package->id,
            'name' => $package->name,
            'description' => $package->description,
            'base_price' => $package->base_price,
            'monthly_price' => $package->base_price,
            'annual_price' => $package->base_price * 12 * 0.9,
            'features' => $package->features,
        ]);
    }

    /**
     * Check domain availability via AJAX
     */
    public function checkDomain(Request $request)
    {
        $domain = $request->query('domain');
        
        if (!$domain || strlen($domain) < 3) {
            return response()->json(['available' => false, 'message' => 'Domain terlalu pendek']);
        }
        
        // Check if domain exists in our services table
        $localExists = Service::where('domain', $domain)->exists();
        
        if ($localExists) {
            return response()->json([
                'available' => false,
                'message' => 'Domain sudah terdaftar di sistem kami',
                'local_check' => 'taken',
                'global_check' => 'unknown'
            ]);
        }
        
        // Check global domain availability via DNS lookup
        $globalAvailable = $this->checkGlobalDomainAvailability($domain);
        
        if (!$globalAvailable) {
            return response()->json([
                'available' => false,
                'message' => 'Domain sudah terdaftar secara global',
                'local_check' => 'available',
                'global_check' => 'taken'
            ]);
        }
        
        return response()->json([
            'available' => true,
            'message' => 'Domain tersedia (belum terdaftar di sistem kami maupun global)',
            'local_check' => 'available',
            'global_check' => 'available'
        ]);
    }
    
    /**
     * Check global domain availability via DNS lookup
     */
    private function checkGlobalDomainAvailability($domain)
    {
        // Add protocol if missing for validation
        if (!preg_match('/^https?:\/\//', $domain)) {
            $domain = 'https://' . $domain;
        }
        
        // Extract domain from URL
        $parsedUrl = parse_url($domain);
        $hostname = $parsedUrl['host'] ?? $domain;
        
        // Remove https:// if still present
        $hostname = str_replace(['https://', 'http://'], '', $hostname);
        
        // Validate domain format
        if (!filter_var($hostname, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return false;
        }
        
        // Check cache first (5 minutes cache)
        $cacheKey = 'domain_check_' . md5($hostname);
        $cached = cache()->get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }
        
        try {
            // Set timeout for DNS operations
            $originalTimeout = ini_get('default_socket_timeout');
            ini_set('default_socket_timeout', 3);
            
            // Check DNS records (A, AAAA, CNAME, MX, NS)
            $hasDnsRecords = false;
            
            // Check A record
            if (@checkdnsrr($hostname, 'A')) {
                $hasDnsRecords = true;
            }
            
            // Check AAAA record (IPv6)
            if (!$hasDnsRecords && @checkdnsrr($hostname, 'AAAA')) {
                $hasDnsRecords = true;
            }
            
            // Check CNAME record
            if (!$hasDnsRecords && @checkdnsrr($hostname, 'CNAME')) {
                $hasDnsRecords = true;
            }
            
            // Check MX record (mail)
            if (!$hasDnsRecords && @checkdnsrr($hostname, 'MX')) {
                $hasDnsRecords = true;
            }
            
            // Check NS record (nameservers)
            if (!$hasDnsRecords && @checkdnsrr($hostname, 'NS')) {
                $hasDnsRecords = true;
            }
            
            // Restore original timeout
            ini_set('default_socket_timeout', $originalTimeout);
            
            // If no DNS records found, domain is likely available
            $isAvailable = !$hasDnsRecords;
            
            // Cache result for 5 minutes
            cache()->put($cacheKey, $isAvailable, 300);
            
            return $isAvailable;
            
        } catch (\Exception $e) {
            // Restore original timeout on error
            ini_set('default_socket_timeout', $originalTimeout);
            
            // On DNS failure, assume domain is taken (safer approach)
            return false;
        }
    }
}
