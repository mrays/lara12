<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceUpgradeRequest;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceUpgradeController extends Controller
{
    /**
     * Display all upgrade requests for admin
     */
    public function index(Request $request)
    {
        $query = ServiceUpgradeRequest::with(['service', 'client', 'processedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('service', function ($q) use ($search) {
                $q->where('product', 'like', "%{$search}%");
            })->orWhere('current_plan', 'like', "%{$search}%")
              ->orWhere('requested_plan', 'like', "%{$search}%");
        }

        $upgradeRequests = $query->with(['service', 'client', 'processedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get counts for status badges
        $statusCounts = [
            'all' => ServiceUpgradeRequest::count(),
            'pending' => ServiceUpgradeRequest::where('status', 'pending')->count(),
            'approved' => ServiceUpgradeRequest::where('status', 'approved')->count(),
            'rejected' => ServiceUpgradeRequest::where('status', 'rejected')->count(),
            'processing' => ServiceUpgradeRequest::where('status', 'processing')->count(),
            'cancelled' => ServiceUpgradeRequest::where('status', 'cancelled')->count(),
        ];

        return view('admin.upgrade-requests.index', compact('upgradeRequests', 'statusCounts'));
    }

    /**
     * Show specific upgrade request details
     */
    public function show(ServiceUpgradeRequest $upgradeRequest)
    {
        $upgradeRequest->load(['service', 'client', 'processedBy']);
        
        return view('admin.upgrade-requests.show', compact('upgradeRequest'));
    }

    /**
     * Approve upgrade request
     */
    public function approve(Request $request, ServiceUpgradeRequest $upgradeRequest)
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if (!$upgradeRequest->canBeProcessed()) {
            return response()->json([
                'success' => false,
                'message' => 'This request cannot be processed. It may have already been handled.'
            ], 400);
        }

        try {
            DB::transaction(function () use ($upgradeRequest, $validated) {
                // Approve the request
                $upgradeRequest->approve(Auth::id(), $validated['admin_notes']);

                // Update the service with new plan details
                $service = $upgradeRequest->service;
                $service->update([
                    'product' => $upgradeRequest->requested_plan,
                    'price' => $upgradeRequest->requested_price,
                ]);

                // TODO: Generate invoice for the upgrade
                // TODO: Send notification to client
            });

            return response()->json([
                'success' => true,
                'message' => 'Upgrade request approved successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve upgrade request. Please try again.'
            ], 500);
        }
    }

    /**
     * Reject upgrade request
     */
    public function reject(Request $request, ServiceUpgradeRequest $upgradeRequest)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        if (!$upgradeRequest->canBeProcessed()) {
            return response()->json([
                'success' => false,
                'message' => 'This request cannot be processed. It may have already been handled.'
            ], 400);
        }

        try {
            $upgradeRequest->reject(Auth::id(), $validated['admin_notes']);

            // TODO: Send notification to client

            return response()->json([
                'success' => true,
                'message' => 'Upgrade request rejected successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject upgrade request. Please try again.'
            ], 500);
        }
    }

    /**
     * Mark request as processing
     */
    public function markAsProcessing(Request $request, ServiceUpgradeRequest $upgradeRequest)
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if (!$upgradeRequest->canBeProcessed()) {
            return response()->json([
                'success' => false,
                'message' => 'This request cannot be processed. It may have already been handled.'
            ], 400);
        }

        try {
            $upgradeRequest->update([
                'status' => 'processing',
                'admin_notes' => $validated['admin_notes'],
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request marked as processing!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update request status.'
            ], 500);
        }
    }

    /**
     * Bulk delete upgrade requests
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:service_upgrade_requests,id'
        ]);

        $count = count($request->ids);
        ServiceUpgradeRequest::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.upgrade-requests.index')
            ->with('success', "{$count} request(s) deleted successfully");
    }

    /**
     * Bulk actions for multiple requests
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|string|in:approve,reject,mark_processing',
            'request_ids' => 'required|array|min:1',
            'request_ids.*' => 'exists:service_upgrade_requests,id',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $requestIds = $validated['request_ids'];
        $action = $validated['action'];
        $adminNotes = $validated['admin_notes'] ?? '';

        try {
            DB::transaction(function () use ($requestIds, $action, $adminNotes) {
                $requests = ServiceUpgradeRequest::whereIn('id', $requestIds)
                    ->where('status', 'pending')
                    ->get();

                foreach ($requests as $upgradeRequest) {
                    switch ($action) {
                        case 'approve':
                            $upgradeRequest->approve(Auth::id(), $adminNotes);
                            break;
                        case 'reject':
                            $upgradeRequest->reject(Auth::id(), $adminNotes);
                            break;
                        case 'mark_processing':
                            $upgradeRequest->update([
                                'status' => 'processing',
                                'admin_notes' => $adminNotes,
                                'processed_by' => Auth::id(),
                                'processed_at' => now(),
                            ]);
                            break;
                    }
                }
            });

            $actionText = match($action) {
                'approve' => 'approved',
                'reject' => 'rejected',
                'mark_processing' => 'marked as processing',
            };

            return response()->json([
                'success' => true,
                'message' => count($requestIds) . " request(s) {$actionText} successfully!"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process bulk action. Please try again.'
            ], 500);
        }
    }
}
