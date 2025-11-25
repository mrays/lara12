<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceUpgradeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceUpgradeController extends Controller
{
    /**
     * Submit upgrade request from client
     */
    public function submitRequest(Request $request, Service $service)
    {
        // Validate that the service belongs to the authenticated user
        if ($service->client_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this service.'
            ], 403);
        }

        // Validate request data
        $validated = $request->validate([
            'requested_plan' => 'required|string|max:255',
            'requested_price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|string|in:monthly,annually',
            'upgrade_reason' => 'required|string|in:need_more_resources,additional_features,business_growth,performance_improvement,other',
            'additional_notes' => 'nullable|string|max:1000',
        ]);

        // Check if there's already a pending request for this service
        $existingRequest = ServiceUpgradeRequest::where('service_id', $service->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending upgrade request for this service. Please wait for admin approval.'
            ], 400);
        }

        try {
            // Create upgrade request
            $upgradeRequest = ServiceUpgradeRequest::create([
                'service_id' => $service->id,
                'client_id' => Auth::id(),
                'current_plan' => $service->product,
                'requested_plan' => $validated['requested_plan'],
                'current_price' => $service->price,
                'requested_price' => $validated['requested_price'],
                'upgrade_reason' => $validated['upgrade_reason'],
                'additional_notes' => $validated['additional_notes'],
                'status' => 'pending',
            ]);

            // TODO: Send notification to admin (implement later)
            
            return response()->json([
                'success' => true,
                'message' => 'Upgrade request submitted successfully! Our admin team will review it shortly.',
                'request_id' => $upgradeRequest->id
            ]);

        } catch (\Exception $e) {
            // Log the actual error for debugging
            \Log::error('Upgrade request submission failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit upgrade request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get client's upgrade requests
     */
    public function clientRequests()
    {
        $requests = ServiceUpgradeRequest::with(['service', 'processedBy'])
            ->where('client_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('client.upgrade-requests.index', compact('requests'));
    }

    /**
     * Show specific upgrade request for client
     */
    public function clientShow(ServiceUpgradeRequest $request)
    {
        // Validate that the request belongs to the authenticated user
        if ($request->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this upgrade request.');
        }

        $request->load(['service', 'processedBy']);
        
        return view('client.upgrade-requests.show', compact('request'));
    }

    /**
     * Check upgrade request status (for auto-refresh)
     */
    public function checkUpgradeStatus(Service $service)
    {
        // Validate that the service belongs to the authenticated user
        if ($service->client_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this service.'
            ], 403);
        }

        $upgradeRequest = ServiceUpgradeRequest::where('service_id', $service->id)
            ->where('client_id', Auth::id())
            ->whereIn('status', ['pending', 'approved', 'processing'])
            ->first();

        return response()->json([
            'hasUpgradeRequest' => $upgradeRequest !== null,
            'status' => $upgradeRequest ? $upgradeRequest->status : null,
            'request_id' => $upgradeRequest ? $upgradeRequest->id : null,
        ]);
    }

    /**
     * Cancel upgrade request (only if pending)
     */
    public function cancel(ServiceUpgradeRequest $upgradeRequest)
    {
        // Validate that the request belongs to the authenticated user
        if ($upgradeRequest->client_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this upgrade request.'
            ], 403);
        }

        if ($upgradeRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending requests can be cancelled.'
            ], 400);
        }

        try {
            $upgradeRequest->update([
                'status' => 'cancelled',
                'admin_notes' => 'Cancelled by client',
                'processed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Upgrade request cancelled successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel upgrade request.'
            ], 500);
        }
    }
}
