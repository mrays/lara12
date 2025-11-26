@extends('layouts.admin')

@section('title', 'Service Upgrade Requests')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="bx bx-credit-card me-2"></i>Service Upgrade Requests
                        </h5>
                        <small class="text-muted">Manage client service upgrade requests</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm" onclick="refreshPage()">
                            <i class="bx bx-refresh me-1"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Total Requests</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ $statusCounts['all'] }}</h4>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-list-ul bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Pending</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ $statusCounts['pending'] }}</h4>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-time bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Approved</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ $statusCounts['approved'] }}</h4>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-check-circle bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Processing</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ $statusCounts['processing'] }}</h4>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-cog bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Rejected</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ $statusCounts['rejected'] }}</h4>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="bx bx-x-circle bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.upgrade-requests.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Status Filter</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by client name, email, or service..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-search me-1"></i>Filter
                        </button>
                        <a href="{{ route('admin.upgrade-requests.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-x me-1"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Upgrade Requests Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Upgrade Requests</h5>
            <div class="d-flex flex-wrap gap-1 gap-md-2 align-items-center">
                <button class="btn btn-danger btn-sm" onclick="deleteSelected()" id="bulkDeleteBtn" style="display: none;">
                    <i class="bx bx-trash"></i>
                    <span class="d-none d-lg-inline ms-1">Delete</span>
                    (<span id="selectedCount">0</span>)
                </button>
                <button class="btn btn-success btn-sm" onclick="bulkAction('approve')" id="bulkApproveBtn" style="display: none;">
                    <i class="bx bx-check"></i>
                    <span class="d-none d-lg-inline ms-1">Approve</span>
                </button>
                <button class="btn btn-warning btn-sm" onclick="bulkAction('reject')" id="bulkRejectBtn" style="display: none;">
                    <i class="bx bx-x"></i>
                    <span class="d-none d-lg-inline ms-1">Reject</span>
                </button>
                <button class="btn btn-info btn-sm" onclick="bulkAction('mark_processing')" id="bulkProcessingBtn" style="display: none;">
                    <i class="bx bx-cog"></i>
                    <span class="d-none d-lg-inline ms-1">Processing</span>
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($upgradeRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="45" class="text-center">
                                    <input type="checkbox" id="selectAll" class="form-check-input cursor-pointer" style="width: 18px; height: 18px;">
                                </th>
                                <th>Request ID</th>
                                <th>Type</th>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Current → Requested</th>
                                <th>Price Change</th>
                                <th>Reason/Notes</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upgradeRequests as $request)
                            <tr>
                                <td class="text-center">
                                    @if($request->status === 'pending')
                                        <input type="checkbox" class="form-check-input request-checkbox cursor-pointer" value="{{ $request->id }}" style="width: 18px; height: 18px;">
                                    @endif
                                </td>
                                <td>
                                    <strong>#{{ $request->id }}</strong>
                                </td>
                                <td>
                                    @if($request->request_type === 'cancellation')
                                        <span class="badge bg-danger">
                                            <i class="bx bx-x-circle me-1"></i>Pembatalan
                                        </span>
                                    @elseif($request->request_type === 'renewal')
                                        <span class="badge bg-success">
                                            <i class="bx bx-refresh me-1"></i>Perpanjangan
                                        </span>
                                    @else
                                        <span class="badge bg-primary">
                                            <i class="bx bx-credit-card me-1"></i>Upgrade
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($request->client)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ substr($request->client->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $request->client->name }}</h6>
                                                <small class="text-muted">{{ $request->client->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-muted">
                                            <i class="bx bx-user-x me-1"></i>
                                            Client not found
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($request->service)
                                        <strong>{{ $request->service->product }}</strong><br>
                                        <small class="text-muted">Service #{{ $request->service->id }}</small>
                                    @else
                                        <div class="text-muted">
                                            <i class="bx bx-package me-1"></i>
                                            Service not found
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($request->request_type === 'cancellation')
                                            <span class="badge bg-light text-dark">{{ $request->current_plan }}</span>
                                            <i class="bx bx-right-arrow-alt mx-2 text-danger"></i>
                                            <span class="badge bg-danger">
                                                <i class="bx bx-x-circle me-1"></i>CANCELLED
                                            </span>
                                        @elseif($request->request_type === 'renewal')
                                            <span class="badge bg-light text-dark">{{ $request->current_plan }}</span>
                                            <i class="bx bx-refresh mx-2 text-success"></i>
                                            <span class="badge bg-success">
                                                <i class="bx bx-refresh me-1"></i>RENEWAL
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark">{{ $request->current_plan }}</span>
                                            <i class="bx bx-right-arrow-alt mx-2"></i>
                                            <span class="badge bg-primary">{{ $request->requested_plan }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($request->request_type === 'cancellation')
                                        <span class="fw-bold text-danger">
                                            <i class="bx bx-x-circle me-1"></i>Cancellation
                                        </span>
                                    @elseif($request->request_type === 'renewal')
                                        <span class="fw-bold text-success">
                                            Rp {{ number_format($request->requested_price, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="fw-bold {{ $request->price_difference >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $request->formatted_price_difference }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        @if($request->request_type === 'renewal' && $request->additional_notes)
                                            {{ Str::limit($request->additional_notes, 50) }}
                                        @elseif($request->request_type === 'cancellation' && $request->additional_notes)
                                            {{ Str::limit($request->additional_notes, 50) }}
                                        @elseif($request->upgrade_reason && !in_array($request->upgrade_reason, ['Cancellation', 'Renewal']))
                                            {{ $request->upgrade_reason }}
                                        @else
                                            -
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <span class="badge {{ $request->status_badge_class }}">
                                        {{ $request->status_text }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $request->created_at->format('M d, Y') }}</small><br>
                                    <small class="text-muted">{{ $request->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.upgrade-requests.show', $request) }}">
                                                    <i class="bx bx-show me-2"></i>View Details
                                                </a>
                                            </li>
                                            @if($request->status === 'pending')
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item text-success" href="#" onclick="quickAction('approve', {{ $request->id }})">
                                                        <i class="bx bx-check me-2"></i>Approve
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" onclick="quickAction('reject', {{ $request->id }})">
                                                        <i class="bx bx-x me-2"></i>Reject
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-info" href="#" onclick="quickAction('processing', {{ $request->id }})">
                                                        <i class="bx bx-cog me-2"></i>Mark as Processing
                                                    </a>
                                                </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" onclick="deleteRequest({{ $request->id }})">
                                                    <i class="bx bx-trash me-2"></i>Delete Request
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="avatar avatar-xl mx-auto mb-3">
                        <span class="avatar-initial rounded-circle bg-label-secondary">
                            <i class="bx bx-search bx-lg"></i>
                        </span>
                    </div>
                    <h5>No upgrade requests found</h5>
                    <p class="text-muted">No service upgrade requests match your current filters.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Quick Action Modal -->
<div class="modal fade" id="quickActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickActionTitle">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickActionForm">
                <div class="modal-body">
                    <p id="quickActionMessage"></p>
                    <div class="mb-3">
                        <label class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="quickActionNotes" rows="3" 
                                  placeholder="Add notes about this action..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="quickActionSubmit">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentAction = '';
let currentRequestId = '';

// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.request-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    toggleBulkButtons();
});

// Individual checkbox change
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('request-checkbox')) {
        toggleBulkButtons();
    }
});

function toggleBulkButtons() {
    const checkedBoxes = document.querySelectorAll('.request-checkbox:checked');
    const bulkButtons = ['bulkDeleteBtn', 'bulkApproveBtn', 'bulkRejectBtn', 'bulkProcessingBtn'];
    const count = checkedBoxes.length;
    
    document.getElementById('selectedCount').textContent = count;
    
    bulkButtons.forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (count > 0) {
            btn.style.display = 'inline-block';
        } else {
            btn.style.display = 'none';
        }
    });
}

function deleteSelected() {
    const checkedBoxes = document.querySelectorAll('.request-checkbox:checked');
    const requestIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (requestIds.length === 0) {
        showToast('Please select at least one request', 'warning');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${requestIds.length} request(s)? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.upgrade-requests.bulk-delete") }}';
        form.innerHTML = `@csrf`;
        
        requestIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteRequest(requestId) {
    if (confirm('Are you sure you want to delete this upgrade request? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/upgrade-requests/${requestId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function quickAction(action, requestId) {
    currentAction = action;
    currentRequestId = requestId;
    
    const actionTexts = {
        'approve': 'approve this upgrade request',
        'reject': 'reject this upgrade request',
        'processing': 'mark this request as processing'
    };
    
    document.getElementById('quickActionTitle').textContent = `Confirm ${action.charAt(0).toUpperCase() + action.slice(1)}`;
    document.getElementById('quickActionMessage').textContent = `Are you sure you want to ${actionTexts[action]}?`;
    
    const modal = new bootstrap.Modal(document.getElementById('quickActionModal'));
    modal.show();
}

document.getElementById('quickActionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const notes = document.getElementById('quickActionNotes').value;
    const submitBtn = document.getElementById('quickActionSubmit');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    
    fetch(`/admin/upgrade-requests/${currentRequestId}/${currentAction}`, {
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
        submitBtn.innerHTML = 'Confirm';
        bootstrap.Modal.getInstance(document.getElementById('quickActionModal')).hide();
    });
});

function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.request-checkbox:checked');
    const requestIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (requestIds.length === 0) {
        showToast('Please select at least one request', 'warning');
        return;
    }
    
    const notes = prompt(`Add notes for this bulk ${action} action:`);
    if (notes === null) return; // User cancelled
    
    fetch('/admin/upgrade-requests/bulk-action', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            action: action,
            request_ids: requestIds,
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
    });
}

function refreshPage() {
    location.reload();
}

function showToast(message, type) {
    // Simple toast implementation - you can replace with your preferred toast library
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
