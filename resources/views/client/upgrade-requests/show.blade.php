@extends('layouts.sneat-dashboard')

@section('title', 'Upgrade Request Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="bx bx-up-arrow-alt me-2"></i>Upgrade Request #{{ $request->id }}
                        </h5>
                        <small class="text-muted">Submitted on {{ $request->created_at->format('M d, Y \a\t H:i') }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('client.upgrade-requests.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to List
                        </a>
                        <a href="{{ route('client.services.manage', $request->service) }}" class="btn btn-primary">
                            <i class="bx bx-globe me-1"></i>Manage Service
                        </a>
                        @if($request->status === 'pending')
                            <button class="btn btn-danger" onclick="cancelRequest({{ $request->id }})">
                                <i class="bx bx-x me-1"></i>Cancel Request
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Request Details -->
        <div class="col-lg-8">
            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Request Status</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="badge {{ $request->status_badge_class }} fs-6">
                                {{ $request->status_text }}
                            </span>
                        </div>
                        @if($request->processed_at)
                            <div class="text-end">
                                <small class="text-muted">
                                    Processed on {{ $request->processed_at->format('M d, Y \a\t H:i') }}
                                    @if($request->processedBy)
                                        by {{ $request->processedBy->name }}
                                    @endif
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Service Comparison -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Service Upgrade Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="text-center">
                                <h6 class="text-muted">Current Plan</h6>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="mb-2">{{ $request->current_plan }}</h5>
                                        <h4 class="text-primary mb-0">
                                            Rp {{ number_format($request->current_price, 0, ',', '.') }}
                                        </h4>
                                        <small class="text-muted">per month</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-center justify-content-center">
                            <i class="bx bx-right-arrow-alt bx-lg text-primary"></i>
                        </div>
                        <div class="col-md-5">
                            <div class="text-center">
                                <h6 class="text-muted">Requested Plan</h6>
                                <div class="card border-primary">
                                    <div class="card-body">
                                        <h5 class="mb-2">{{ $request->requested_plan }}</h5>
                                        <h4 class="text-success mb-0">
                                            Rp {{ number_format($request->requested_price, 0, ',', '.') }}
                                        </h4>
                                        <small class="text-muted">per month</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Price Difference -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-info-circle me-2"></i>
                                    <div>
                                        <strong>Price Difference:</strong> 
                                        <span class="fw-bold {{ $request->price_difference >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $request->formatted_price_difference }}
                                        </span>
                                        per month
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Request Reason -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Upgrade Reason</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Primary Reason</label>
                        <div class="form-control-plaintext">
                            @switch($request->upgrade_reason)
                                @case('need_more_resources')
                                    <i class="bx bx-server me-2"></i>Need More Resources
                                    @break
                                @case('additional_features')
                                    <i class="bx bx-plus-circle me-2"></i>Need Additional Features
                                    @break
                                @case('business_growth')
                                    <i class="bx bx-trending-up me-2"></i>Business Growth
                                    @break
                                @case('performance_improvement')
                                    <i class="bx bx-tachometer me-2"></i>Performance Improvement
                                    @break
                                @case('other')
                                    <i class="bx bx-help-circle me-2"></i>Other
                                    @break
                                @default
                                    {{ ucfirst(str_replace('_', ' ', $request->upgrade_reason)) }}
                            @endswitch
                        </div>
                    </div>

                    @if($request->additional_notes)
                        <div class="mb-3">
                            <label class="form-label">Additional Notes</label>
                            <div class="form-control-plaintext">
                                {{ $request->additional_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Admin Notes -->
            @if($request->admin_notes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Admin Notes</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $request->admin_notes }}</p>
                    </div>
                </div>
            @endif

            <!-- Status Messages -->
            @if($request->status === 'approved')
                <div class="card border-success mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center text-success">
                            <i class="bx bx-check-circle me-2 fs-4"></i>
                            <div>
                                <h6 class="mb-1">Upgrade Approved!</h6>
                                <p class="mb-0">Your upgrade request has been approved by our admin team. 
                                <a href="{{ route('client.invoices.index') }}" class="alert-link">Check your invoices</a> for payment details.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($request->status === 'processing')
                <div class="card border-info mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center text-info">
                            <i class="bx bx-cog me-2 fs-4"></i>
                            <div>
                                <h6 class="mb-1">Currently Processing</h6>
                                <p class="mb-0">Your upgrade request is being processed by our team. We'll notify you once it's completed.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($request->status === 'rejected')
                <div class="card border-danger mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center text-danger">
                            <i class="bx bx-x-circle me-2 fs-4"></i>
                            <div>
                                <h6 class="mb-1">Request Rejected</h6>
                                <p class="mb-0">Your upgrade request has been rejected. Please check the admin notes above for more information.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Service Info -->
        <div class="col-lg-4">
            <!-- Service Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Service Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Service ID:</small>
                        <div><strong>#{{ $request->service->id }}</strong></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Product:</small>
                        <div>{{ $request->service->product }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Domain:</small>
                        <div>{{ $request->service->domain ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Status:</small>
                        <div>
                            @switch($request->service->status)
                                @case('Active')
                                    <span class="badge bg-success">Active</span>
                                    @break
                                @case('Suspended')
                                    <span class="badge bg-warning">Suspended</span>
                                    @break
                                @case('Terminated')
                                    <span class="badge bg-danger">Terminated</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ $request->service->status }}</span>
                            @endswitch
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Billing Cycle:</small>
                        <div>{{ $request->service->translated_billing_cycle }}</div>
                    </div>
                    @if($request->service->due_date)
                        <div class="mb-3">
                            <small class="text-muted">Next Due Date:</small>
                            <div>{{ $request->service->due_date->format('M d, Y') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('client.services.manage', $request->service) }}" class="btn btn-primary">
                            <i class="bx bx-globe me-2"></i>Manage Service
                        </a>
                        <a href="{{ route('client.invoices.index') }}" class="btn btn-outline-primary">
                            <i class="bx bx-receipt me-2"></i>View Invoices
                        </a>
                        @if($request->status === 'pending')
                            <button class="btn btn-outline-danger" onclick="cancelRequest({{ $request->id }})">
                                <i class="bx bx-x me-2"></i>Cancel Request
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cancelRequest(requestId) {
    if (confirm('Are you sure you want to cancel this upgrade request?')) {
        fetch(`/upgrade-requests/${requestId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.href = '{{ route('client.upgrade-requests.index') }}', 1000);
            } else {
                showToast(data.message, 'danger');
            }
        })
        .catch(error => {
            showToast('An error occurred', 'danger');
        });
    }
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.innerHTML = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>
@endsection
