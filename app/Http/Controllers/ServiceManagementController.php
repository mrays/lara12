<?php

namespace App\Http\Controllers;

use App\Models\Service;
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

        // Get service with related data
        $service->load(['client', 'invoices' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(5);
        }]);

        return view('client.services.manage', compact('service'));
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
