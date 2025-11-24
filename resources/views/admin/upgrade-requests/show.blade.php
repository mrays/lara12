@extends('layouts.admin')

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
                            <i class="bx bx-up-arrow-alt me-2"></i>Upgrade Request #{{ $upgradeRequest->id }}
                        </h5>
                        <small class="text-muted">Submitted on {{ $upgradeRequest->created_at->format('M d, Y \a\t H:i') }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.upgrade-requests.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to List
                        </a>
                        @if($upgradeRequest->status === 'pending')
                            <button class="btn btn-success" onclick="processRequest('approve')">
                                <i class="bx bx-check me-1"></i>Approve
                            </button>
                            <button class="btn btn-info" onclick="processRequest('processing')">
                                <i class="bx bx-cog me-1"></i>Mark as Processing
                            </button>
                            <button class="btn btn-danger" onclick="processRequest('reject')">
                                <i class="bx bx-x me-1"></i>Reject
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
                            <span class="badge {{ $upgradeRequest->status_badge_class }} fs-6">
                                {{ $upgradeRequest->status_text }}
                            </span>
                        </div>
                        @if($upgradeRequest->processed_at)
                            <div class="text-end">
                                <small class="text-muted">
                                    Processed on {{ $upgradeRequest->processed_at->format('M d, Y \a\t H:i') }}
                                    @if($upgradeRequest->processedBy)
                                        by {{ $upgradeRequest->processedBy->name }}
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
                                        <h5 class="mb-2">{{ $upgradeRequest->current_plan }}</h5>
                                        <h4 class="text-primary mb-0">
                                            Rp {{ number_format($upgradeRequest->current_price, 0, ',', '.') }}
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
                                        <h5 class="mb-2">{{ $upgradeRequest->requested_plan }}</h5>
                                        <h4 class="text-success mb-0">
                                            Rp {{ number_format($upgradeRequest->requested_price, 0, ',', '.') }}
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
                                        <span class="fw-bold {{ $upgradeRequest->price_difference >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $upgradeRequest->formatted_price_difference }}
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
                            @switch($upgradeRequest->upgrade_reason)
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
                                    {{ ucfirst(str_replace('_', ' ', $upgradeRequest->upgrade_reason)) }}
                            @endswitch
                        </div>
                    </div>

                    @if($upgradeRequest->additional_notes)
                        <div class="mb-3">
                            <label class="form-label">Additional Notes</label>
                            <div class="form-control-plaintext">
                                {{ $upgradeRequest->additional_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Admin Notes -->
            @if($upgradeRequest->admin_notes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Admin Notes</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $upgradeRequest->admin_notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Client & Service Info -->
        <div class="col-lg-4">
            <!-- Client Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Client Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-lg me-3">
                            <span class="avatar-initial rounded-circle bg-label-primary">
                                {{ substr($upgradeRequest->client->name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $upgradeRequest->client->name }}</h6>
                            <small class="text-muted">{{ $upgradeRequest->client->email }}</small>
                        </div>
                    </div>

                    <div class="row g-2">
                        @if($upgradeRequest->client->phone)
                            <div class="col-12">
                                <small class="text-muted">Phone:</small>
                                <div>{{ $upgradeRequest->client->phone }}</div>
                            </div>
                        @endif
                        @if($upgradeRequest->client->business_name)
                            <div class="col-12">
                                <small class="text-muted">Business:</small>
                                <div>{{ $upgradeRequest->client->business_name }}</div>
                            </div>
                        @endif
                        <div class="col-12">
                            <small class="text-muted">Member since:</small>
                            <div>{{ $upgradeRequest->client->created_at->format('M Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Service Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Service ID:</small>
                        <div><strong>#{{ $upgradeRequest->service->id }}</strong></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Product:</small>
                        <div>{{ $upgradeRequest->service->product }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Status:</small>
                        <div>
                            @switch($upgradeRequest->service->status)
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
                                    <span class="badge bg-secondary">{{ $upgradeRequest->service->status }}</span>
                            @endswitch
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Billing Cycle:</small>
                        <div>{{ ucfirst($upgradeRequest->service->billing_cycle) }}</div>
                    </div>
                    @if($upgradeRequest->service->next_due_date)
                        <div class="mb-3">
                            <small class="text-muted">Next Due Date:</small>
                            <div>{{ $upgradeRequest->service->next_due_date->format('M d, Y') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            @if($upgradeRequest->status === 'pending')
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-success" onclick="processRequest('approve')">
                                <i class="bx bx-check me-2"></i>Approve Request
                            </button>
                            <button class="btn btn-info" onclick="processRequest('processing')">
                                <i class="bx bx-cog me-2"></i>Mark as Processing
                            </button>
                            <button class="btn btn-outline-danger" onclick="processRequest('reject')">
                                <i class="bx bx-x me-2"></i>Reject Request
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Process Request Modal -->
<div class="modal fade" id="processRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="processRequestTitle">Process Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="processRequestForm">
                <div class="modal-body">
                    <p id="processRequestMessage"></p>
                    <div class="mb-3">
                        <label class="form-label">Admin Notes <span class="text-danger" id="notesRequired" style="display: none;">*</span></label>
                        <textarea class="form-control" id="processRequestNotes" rows="4" 
                                  placeholder="Add notes about this decision..."></textarea>
                        <small class="text-muted">These notes will be visible to the client.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="processRequestSubmit">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentProcessAction = '';

function processRequest(action) {
    currentProcessAction = action;
    
    const actionTexts = {
        'approve': {
            title: 'Approve Upgrade Request',
            message: 'Are you sure you want to approve this upgrade request? This will allow the client to proceed with the upgrade.',
            btnClass: 'btn-success',
            btnText: 'Approve Request'
        },
        'reject': {
            title: 'Reject Upgrade Request',
            message: 'Are you sure you want to reject this upgrade request? Please provide a reason for the rejection.',
            btnClass: 'btn-danger',
            btnText: 'Reject Request'
        },
        'processing': {
            title: 'Mark as Processing',
            message: 'Mark this request as currently being processed. This indicates that you are working on it.',
            btnClass: 'btn-info',
            btnText: 'Mark as Processing'
        }
    };
    
    const config = actionTexts[action];
    
    document.getElementById('processRequestTitle').textContent = config.title;
    document.getElementById('processRequestMessage').textContent = config.message;
    
    const submitBtn = document.getElementById('processRequestSubmit');
    submitBtn.className = `btn ${config.btnClass}`;
    submitBtn.textContent = config.btnText;
    
    // Show required indicator for reject action
    const notesRequired = document.getElementById('notesRequired');
    if (action === 'reject') {
        notesRequired.style.display = 'inline';
        document.getElementById('processRequestNotes').required = true;
    } else {
        notesRequired.style.display = 'none';
        document.getElementById('processRequestNotes').required = false;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('processRequestModal'));
    modal.show();
}

document.getElementById('processRequestForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const notes = document.getElementById('processRequestNotes').value;
    const submitBtn = document.getElementById('processRequestSubmit');
    
    // Validate required notes for reject action
    if (currentProcessAction === 'reject' && !notes.trim()) {
        showToast('Admin notes are required when rejecting a request', 'warning');
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    
    fetch(`/admin/upgrade-requests/{{ $upgradeRequest->id }}/${currentProcessAction}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            admin_notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message, 'danger');
        }
    })
    .catch(error => {
        showToast('An error occurred', 'danger');
    })
    .finally(() => {
        submitBtn.disabled = false;
        const config = {
            'approve': 'Approve Request',
            'reject': 'Reject Request',
            'processing': 'Mark as Processing'
        };
        submitBtn.innerHTML = config[currentProcessAction];
        bootstrap.Modal.getInstance(document.getElementById('processRequestModal')).hide();
    });
});

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
