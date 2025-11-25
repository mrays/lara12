<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServicePackage;
use App\Models\DomainExtension;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Show order form with available service packages
     */
    public function create()
    {
        $packages = ServicePackage::where('is_active', true)
            ->with('domainExtension')
            ->orderBy('base_price', 'asc')
            ->get();
            
        $domainExtensions = DomainExtension::active()
            ->orderBy('extension')
            ->orderBy('duration_years')
            ->get();
            
        // Group domain extensions by extension for better organization
        $groupedDomains = $domainExtensions->groupBy('extension');
            
        return view('client.orders.create', compact('packages', 'domainExtensions', 'groupedDomains'));
    }

    /**
     * Calculate domain price with promo validation
     */
    private function calculateDomainPrice($package, $domainExtension, $selectedDomainId)
    {
        // Check if package has free domains and selected domain matches any of them
        if ($package->freeDomains && $package->freeDomains->isNotEmpty()) {
            $freeDomain = $package->freeDomains
                ->where('domain_extension_id', $selectedDomainId)
                ->first();
                
            if ($freeDomain) {
                // Domain is included in package promo
                if ($freeDomain->is_free) {
                    return 0; // Free domain
                } else {
                    // Apply package discount
                    return $domainExtension->price * (1 - $freeDomain->discount_percent / 100);
                }
            }
        }
        
        // Fallback to old single domain logic for backward compatibility
        if ($package->domain_extension_id && $package->domain_extension_id == $selectedDomainId) {
            if ($package->is_domain_free) {
                return 0; // Free domain
            } else {
                return $domainExtension->price * (1 - $package->domain_discount_percent / 100);
            }
        }
        
        // Domain not included in package, charge full price
        return $domainExtension->price;
    }

    /**
     * Store new order and create invoice
     */
    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:service_packages,id',
            'domain_name' => 'required|string|max:255',
            'domain_extension' => 'required|exists:domain_extensions,id',
            'domain_full' => 'required|string|max:255|unique:services,domain',
            'billing_cycle' => 'required|in:monthly,annually',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
        $package = ServicePackage::with(['domainExtension', 'freeDomains'])->findOrFail($request->package_id);
        $domainExtension = DomainExtension::findOrFail($request->domain_extension);
        
        // Calculate package price
        $packagePrice = $package->getPrice($request->billing_cycle);
        
        // Calculate domain price with promo validation
        $domainPrice = $this->calculateDomainPrice($package, $domainExtension, $request->domain_extension);
        
        // Total price
        $totalPrice = $packagePrice + $domainPrice;
        
        // Ensure price is not null
        if (is_null($packagePrice)) {
            $packagePrice = 0;
        }
        
        if (is_null($totalPrice)) {
            $totalPrice = 0;
        }

        try {
            DB::beginTransaction();

            // Create service with pending status
            $service = Service::create([
                'client_id' => $user->id,
                'package_id' => $package->id,
                'product' => $package->name,
                'domain' => $request->domain_full,
                'price' => $totalPrice,
                'billing_cycle' => $request->billing_cycle,
                'registration_date' => now(),
                'status' => 'Pending',
                'due_date' => now()->addDays(7), // Due in 7 days for first payment
                'notes' => $request->notes,
            ]);

            // Generate invoice number
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            
            // Create invoice using the model (column already exists)
            $invoice = Invoice::create([
                'number' => $invoiceNumber,
                'title' => "Order: {$package->name}",
                'client_id' => $user->id,
                'service_id' => $service->id,
                'subtotal' => $totalPrice,
                'amount' => $totalPrice, // Add amount field for backward compatibility
                'total_amount' => $totalPrice,
                'status' => 'Unpaid',
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(7),
                'description' => "Order: {$package->name} - {$request->domain_full} ({$request->billing_cycle})",
                'notes' => $request->notes,
            ]);

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
