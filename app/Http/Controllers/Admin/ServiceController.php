<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Client;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        // Use direct DB query for compatibility
        $services = \DB::table('services')
            ->leftJoin('users', 'services.client_id', '=', 'users.id')
            ->select('services.*', 'users.name as client_name', 'users.email as client_email')
            ->when($q, function($query) use ($q) {
                return $query->where('services.name', 'like', "%$q%")
                           ->orWhere('services.domain', 'like', "%$q%");
            })
            ->orderBy('services.created_at', 'desc')
            ->paginate(15);
            
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        // Get clients from users table where role is client
        $clients = \DB::table('users')
            ->where('role', 'client')
            ->orderBy('name')
            ->get();
        return view('admin.services.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'=>'required|exists:users,id',
            'product'=>'required|string|max:191',
            'domain'=>'nullable|string|max:191',
            'price'=>'required|numeric',
            'billing_cycle'=>'nullable|string|max:50',
            'registration_date'=>'nullable|date',
            'due_date'=>'nullable|date',
            'ip'=>'nullable|ip',
            'status'=>'required|in:Active,Pending,Suspended,Terminated,Dibatalkan,Disuspen,Sedang Dibuat,Ditutup',
        ]);
        Service::create($data);
        return redirect()->route('admin.services.index')->with('success','Service created');
    }

    public function show(Service $service)
    {
        $service->load('client');
        return view('admin.services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        // Get clients from users table where role is client
        $clients = \DB::table('users')
            ->where('role', 'client')
            ->orderBy('name')
            ->get();
        return view('admin.services.edit', compact('service','clients'));
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'client_id'=>'required|exists:users,id',
            'product'=>'required|string|max:191',
            'domain'=>'nullable|string|max:191',
            'price'=>'required|numeric',
            'billing_cycle'=>'nullable|string|max:50',
            'registration_date'=>'nullable|date',
            'due_date'=>'nullable|date',
            'ip'=>'nullable|ip',
            'status'=>'required|in:Active,Pending,Suspended,Terminated,Dibatalkan,Disuspen,Sedang Dibuat,Ditutup',
        ]);
        $service->update($data);
        return redirect()->route('admin.services.index')->with('success','Service updated');
    }

    /**
     * Manage service details for client view
     */
    public function manageDetails($serviceId)
    {
        $service = \DB::table('services')
            ->leftJoin('users', 'services.client_id', '=', 'users.id')
            ->select('services.*', 'users.name as client_name', 'users.email as client_email')
            ->where('services.id', $serviceId)
            ->first();

        if (!$service) {
            return redirect()->route('admin.services.index')
                ->with('error', 'Service not found');
        }

        return view('admin.services.manage-details', compact('service'));
    }

    /**
     * Update service details for client view
     */
    public function updateDetails(Request $request, $serviceId)
    {
        $validated = $request->validate([
            // Service Information
            'service_name' => 'required|string|max:255',
            'product' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Pending,Suspended,Terminated,Dibatalkan,Disuspen,Sedang Dibuat,Ditutup',
            'next_due' => 'nullable|date',
            
            // Billing Information
            'billing_cycle' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
            
            // Overview Information (Login Details)
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'server' => 'nullable|string|max:255',
            'login_url' => 'nullable|url',
            
            // Additional Details
            'description' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        \DB::table('services')
            ->where('id', $serviceId)
            ->update([
                'product' => $validated['service_name'], // Use service_name for product field
                'domain' => $validated['domain'],
                'status' => $validated['status'],
                'due_date' => $validated['next_due'],
                'billing_cycle' => $validated['billing_cycle'],
                'price' => $validated['price'],
                'updated_at' => now()
            ]);

        return redirect()->route('admin.services.manage-details', $serviceId)
            ->with('success', 'Service details updated successfully');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('success','Service deleted');
    }
}
