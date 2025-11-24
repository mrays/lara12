<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServicePackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceManagementController extends Controller
{
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
            $services = Service::with('client')->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $services = Service::where('client_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
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
}
