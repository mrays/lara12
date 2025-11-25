<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceManagementController extends Controller
{
    /**
     * Translate billing cycle abbreviations to Indonesian
     */
    public function translateBillingCycle($billingCycle)
    {
        return self::staticTranslateBillingCycle($billingCycle);
    }

    /**
     * Static method to translate billing cycle abbreviations to Indonesian
     * This can be called from views
     */
    public static function staticTranslateBillingCycle($billingCycle)
    {
        $cycleMap = [
            '1D' => '1 Hari',
            '1W' => '1 Minggu',
            '2W' => '2 Minggu',
            '3W' => '3 Minggu',
            '1M' => '1 Bulan',
            '2M' => '2 Bulan',
            '3M' => '3 Bulan',
            '6M' => '6 Bulan',
            '1Y' => '1 Tahun',
            '2Y' => '2 Tahun',
            '3Y' => '3 Tahun',
            'Monthly' => 'Bulanan',
            'Quarterly' => 'Triwulan',
            'Semi-Annually' => 'Semester',
            'Annually' => 'Tahunan',
            'Biennially' => '2 Tahunan',
        ];

        return $cycleMap[$billingCycle] ?? $billingCycle;
    }

    /**
     * Display service management page
     */
    public function show(Service $service)
    {
        // Check if user can access this service
        if (Auth::user()->role !== 'admin' && $service->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access to service');
        }

        // Get service with all details using direct query (same as admin view)
        $serviceData = \DB::table('services')
            ->leftJoin('users', 'services.client_id', '=', 'users.id')
            ->select('services.*', 'users.name as client_name', 'users.email as client_email')
            ->where('services.id', $service->id)
            ->first();

        if (!$serviceData) {
            abort(404, 'Service not found');
        }

        // Convert to object for compatibility with view
        $service = (object) [
            'id' => $serviceData->id,
            'client_id' => $serviceData->client_id,
            'product' => $serviceData->product,
            'domain' => $serviceData->domain,
            'price' => $serviceData->price,
            'status' => $serviceData->status,
            'due_date' => $serviceData->due_date ? \Carbon\Carbon::parse($serviceData->due_date) : null,
            'billing_cycle' => $serviceData->billing_cycle,
            'translated_billing_cycle' => $this->translateBillingCycle($serviceData->billing_cycle),
            'created_at' => $serviceData->created_at ? \Carbon\Carbon::parse($serviceData->created_at) : null,
            'updated_at' => $serviceData->updated_at ? \Carbon\Carbon::parse($serviceData->updated_at) : null,
            // Add default values for fields that might not exist in database yet
            'username' => $serviceData->username ?? 'admin',
            'password' => $serviceData->password ?? 'musang',
            'server' => $serviceData->server ?? 'Default Server',
            'login_url' => $serviceData->login_url ?? 'https://example.com/login',
            'description' => $serviceData->description ?? 'Service description for client',
            'notes' => $serviceData->notes ?? 'Premium hosting package',
            'setup_fee' => $serviceData->setup_fee ?? 0,
            // Client info
            'client_name' => $serviceData->client_name,
            'client_email' => $serviceData->client_email,
        ];

        // Get recent invoices
        $invoices = \DB::table('invoices')
            ->where('client_id', $service->client_id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $service->invoices = $invoices;

        // Get available service packages for upgrade
        $servicePackages = ServicePackage::active()
            ->orderBy('base_price', 'asc')
            ->get();

        return view('client.services.manage', compact('service', 'servicePackages'));
    }

    /**
     * Display all client services
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            $services = Service::with('client')->orderBy('created_at', 'desc')->get();
        } else {
            $services = Service::where('client_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('client.services.index', compact('services'));
    }

    /**
     * Request service upgrade
     */
    public function requestUpgrade(Request $request, Service $service)
    {
        // Validate request
        $request->validate([
            'package_id' => 'required|exists:service_packages,id',
            'billing_cycle' => 'required|in:monthly,annually'
        ]);

        // Check if user can access this service
        if (Auth::user()->role !== 'admin' && $service->client_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized access to service'], 403);
        }

        // Get the selected package
        $package = ServicePackage::findOrFail($request->package_id);
        
        // Calculate price based on billing cycle
        $price = $request->billing_cycle === 'annually' 
            ? $package->base_price * 12 * 0.9  // 10% discount for annual
            : $package->base_price;

        // Create upgrade request (you might want to create a separate table for this)
        // For now, we'll just return success response
        
        return response()->json([
            'success' => true,
            'message' => "Upgrade to {$package->name} has been requested successfully!",
            'package_name' => $package->name,
            'price' => $price,
            'billing_cycle' => $request->billing_cycle
        ]);
    }

    /**
     * Update service status or details
     */
    public function update(Request $request, Service $service)
    {
        // Check if user can access this service
        if (Auth::user()->role !== 'admin' && $service->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access to service');
        }

        // Only admin can update service status
        if (Auth::user()->role === 'admin') {
            $request->validate([
                'status' => 'sometimes|in:Active,Suspended,Terminated,Pending'
            ]);

            if ($request->has('status')) {
                $service->update(['status' => $request->status]);
                return redirect()->back()->with('success', 'Service status updated successfully');
            }
        }

        return redirect()->back()->with('error', 'Unauthorized action');
    }

    /**
     * Contact support for service
     */
    public function contactSupport(Service $service)
    {
        // Check if user can access this service
        if (Auth::user()->role !== 'admin' && $service->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access to service');
        }

        // Redirect to support or show support form
        return redirect()->route('support.create', ['service_id' => $service->id])
            ->with('info', 'Please describe your issue with this service');
    }

    /**
     * Create renewal invoice for service
     */
    public function createRenewalInvoice(Request $request, $serviceId)
    {
        \Log::info('Renewal request for service ID: ' . $serviceId);
        
        // Get service data using the same approach as show method
        $serviceData = \DB::table('services')
            ->leftJoin('users', 'services.client_id', '=', 'users.id')
            ->select('services.*', 'users.name as client_name', 'users.email as client_email')
            ->where('services.id', $serviceId)
            ->first();

        if (!$serviceData) {
            \Log::error('Service not found: ' . $serviceId);
            return response()->json([
                'success' => false,
                'message' => 'Service not found.'
            ], 404);
        }

        \Log::info('Service data found: ', (array)$serviceData);

        // Check if user can access this service
        if (Auth::user()->role !== 'admin' && $serviceData->client_id !== Auth::id()) {
            \Log::error('Unauthorized access attempt by user: ' . Auth::id() . ' for service: ' . $serviceId);
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this service.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Generate invoice number
            $lastInvoice = Invoice::orderBy('id', 'desc')->first();
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(($lastInvoice ? $lastInvoice->id + 1 : 1), 4, '0', STR_PAD_LEFT);

            \Log::info('Generated invoice number: ' . $invoiceNumber);

            // Calculate due date based on service billing cycle
            $issueDate = now();
            $dueDate = $this->calculateDueDate($serviceData->billing_cycle, $issueDate);

            \Log::info('Due date calculated: ' . $dueDate->format('Y-m-d'));

            // Create renewal invoice
            $invoice = Invoice::create([
                'client_id' => $serviceData->client_id,
                'service_id' => $serviceData->id,
                'number' => $invoiceNumber,
                'title' => "Perpanjang Layanan - {$serviceData->product}",
                'description' => "Perpanjangan layanan untuk {$serviceData->product}" . ($serviceData->domain ? " ({$serviceData->domain})" : ""),
                'subtotal' => $serviceData->price,
                'amount' => $serviceData->price,
                'tax_rate' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $serviceData->price,
                'status' => 'pending',
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'notes' => "Perpanjangan untuk periode {$this->translateBillingCycle($serviceData->billing_cycle)}",
            ]);

            \Log::info('Invoice created with ID: ' . $invoice->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice perpanjangan berhasil dibuat!',
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoiceNumber,
                'amount' => $serviceData->price,
                'due_date' => $dueDate->format('M d, Y'),
                'payment_url' => route('client.invoices.pay', $invoice->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Renewal invoice creation failed: ' . $e->getMessage());
            \Log::error('Exception trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat invoice perpanjangan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate due date based on billing cycle
     */
    private function calculateDueDate($billingCycle, $issueDate)
    {
        switch (strtoupper($billingCycle)) {
            case '1D':
                return $issueDate->copy()->addDay();
            case '1W':
                return $issueDate->copy()->addWeek();
            case '2W':
                return $issueDate->copy()->addWeeks(2);
            case '3W':
                return $issueDate->copy()->addWeeks(3);
            case '1M':
            case 'MONTHLY':
                return $issueDate->copy()->addMonth();
            case '2M':
                return $issueDate->copy()->addMonths(2);
            case '3M':
            case 'QUARTERLY':
                return $issueDate->copy()->addMonths(3);
            case '6M':
            case 'SEMI-ANNUALLY':
                return $issueDate->copy()->addMonths(6);
            case '1Y':
            case 'ANNUALLY':
                return $issueDate->copy()->addYear();
            case '2Y':
            case 'BIENNIALLY':
                return $issueDate->copy()->addYears(2);
            case '3Y':
                return $issueDate->copy()->addYears(3);
            default:
                return $issueDate->copy()->addMonth(); // Default to 1 month
        }
    }
}
