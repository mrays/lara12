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
use Illuminate\Support\Facades\Cache;
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
        
        if ($globalAvailable === 'unknown') {
            return response()->json([
                'available' => 'unknown',
                'message' => 'Tidak dapat memverifikasi ketersediaan domain global. Silakan coba lagi atau lanjutkan dengan risiko Anda sendiri.',
                'local_check' => 'available',
                'global_check' => 'unknown'
            ]);
        } elseif (!$globalAvailable) {
            return response()->json([
                'available' => false,
                'message' => 'Domain sudah terdaftar secara global (sudah digunakan secara global)',
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
        
        // Convert IDN to punycode for DNS queries
        $hostname = $this->convertToPunycode($hostname);
        
        // Enhanced domain validation for international domains
        if (!$this->isValidDomainFormat($hostname)) {
            return false;
        }
        
        // Check cache first (3 minutes cache for better accuracy)
        $cacheKey = 'domain_check_' . md5($hostname);
        $cached = cache()->get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }
        
        try {
            // Set longer timeout for international DNS operations
            $originalTimeout = ini_get('default_socket_timeout');
            ini_set('default_socket_timeout', 8); // Increased from 3 to 8 seconds
            
            // Check DNS records with improved logic
            $dnsResult = $this->performDnsCheck($hostname);
            
            // Restore original timeout
            ini_set('default_socket_timeout', $originalTimeout);
            
            // Handle DNS check results
            if ($dnsResult === 'timeout') {
                // On timeout, return unknown status rather than blocking
                Log::info("DNS timeout for domain: {$hostname}, marking as unknown");
                cache()->put($cacheKey, 'unknown', 180); // Cache unknown for 3 minutes
                $this->monitorUnknownRate();
                $this->recordUnknownResult($hostname, 'timeout');
                return 'unknown';
            } elseif ($dnsResult === 'error') {
                // On DNS error, log and return unknown
                Log::warning("DNS error for domain: {$hostname}, marking as unknown");
                cache()->put($cacheKey, 'unknown', 180);
                $this->monitorUnknownRate();
                $this->recordUnknownResult($hostname, 'error');
                return 'unknown';
            }
            
            // Cache result for 3 minutes (reduced from 5 for better accuracy)
            cache()->put($cacheKey, $dnsResult, 180);
            
            return $dnsResult;
            
        } catch (\Exception $e) {
            // Restore original timeout on error
            ini_set('default_socket_timeout', $originalTimeout);
            
            Log::error("DNS check exception for {$hostname}: " . $e->getMessage());
            // Return unknown on exception to avoid blocking legitimate registrations
            $this->monitorUnknownRate();
            $this->recordUnknownResult($hostname, 'exception');
            return 'unknown';
        }
    }
    
    /**
     * Enhanced domain format validation for international domains
     */
    private function isValidDomainFormat($hostname)
    {
        // Remove any trailing dots
        $hostname = rtrim($hostname, '.');
        
        // Basic length validation
        if (strlen($hostname) < 3 || strlen($hostname) > 253) {
            return false;
        }
        
        // Check for valid characters including international
        if (!preg_match('/^[a-zA-Z0-9\-\.\p{L}\p{N}]+$/u', $hostname)) {
            return false;
        }
        
        // Check domain structure
        $labels = explode('.', $hostname);
        if (count($labels) < 2) {
            return false;
        }
        
        // Validate each label
        foreach ($labels as $label) {
            if (strlen($label) === 0 || strlen($label) > 63) {
                return false;
            }
            // Labels cannot start or end with hyphen
            if (preg_match('/^-|-$/', $label)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Perform DNS check with better error handling
     */
    private function performDnsCheck($hostname)
    {
        $hasDnsRecords = false;
        $recordTypes = ['A', 'AAAA', 'CNAME', 'MX', 'NS'];
        
        foreach ($recordTypes as $recordType) {
            try {
                // Use @ to suppress warnings, we'll handle errors manually
                $result = @checkdnsrr($hostname, $recordType);
                if ($result) {
                    $hasDnsRecords = true;
                    break;
                }
            } catch (\Exception $e) {
                // Continue to next record type on error
                continue;
            }
        }
        
        // Additional check: try to get DNS records as fallback
        if (!$hasDnsRecords) {
            try {
                $records = @dns_get_record($hostname, DNS_A + DNS_AAAA + DNS_CNAME + DNS_MX + DNS_NS);
                if (!empty($records)) {
                    $hasDnsRecords = true;
                }
            } catch (\Exception $e) {
                // Log but don't fail
                Log::debug("dns_get_record failed for {$hostname}: " . $e->getMessage());
            }
        }
        
        // If no DNS records found, domain is likely available
        return !$hasDnsRecords;
    }
    
    /**
     * Convert international domain name to punycode for DNS queries
     */
    private function convertToPunycode($hostname)
    {
        // Remove any trailing dots
        $hostname = rtrim($hostname, '.');
        
        // Split domain into labels
        $labels = explode('.', $hostname);
        $punycodeLabels = [];
        
        foreach ($labels as $label) {
            // Check if label contains non-ASCII characters
            if (mb_check_encoding($label, 'ASCII')) {
                // ASCII-only label, keep as is
                $punycodeLabels[] = $label;
            } else {
                // Non-ASCII label, convert to punycode
                if (function_exists('idn_to_ascii')) {
                    $punycodeLabel = idn_to_ascii($label, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
                    if ($punycodeLabel !== false) {
                        $punycodeLabels[] = $punycodeLabel;
                    } else {
                        // Fallback if conversion fails
                        $punycodeLabels[] = $label;
                    }
                } else {
                    // Fallback if idn_to_ascii not available
                    $punycodeLabels[] = $label;
                }
            }
        }
        
        return implode('.', $punycodeLabels);
    }
    
    /**
     * Monitor for high unknown rate patterns
     */
    private function monitorUnknownRate()
    {
        $unknownCountKey = 'domain_unknown_count_' . date('Y-m-d-H'); // Per hour
        $currentCount = cache()->increment($unknownCountKey, 1);
        cache()->put($unknownCountKey, $currentCount, 3600); // Update with 1 hour TTL
        
        // If unknown rate is high (>100 per hour), log warning
        if ($currentCount > 100) {
            Log::warning("High domain unknown rate detected: {$currentCount} unknown results in current hour");
        }
    }
    
    /**
     * Record unknown results for monitoring
     */
    private function recordUnknownResult($hostname, $reason)
    {
        $statsKey = 'domain_unknown_stats_' . date('Y-m-d');
        $stats = cache()->get($statsKey, []);
        
        if (!isset($stats[$reason])) {
            $stats[$reason] = 0;
        }
        $stats[$reason]++;
        
        cache()->put($statsKey, $stats, 86400); // Keep for 24 hours
    }
}
