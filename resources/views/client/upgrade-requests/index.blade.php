@extends('layouts.sneat-dashboard')

@section('title', 'My Upgrade Requests')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('client.services.index') }}">My Services</a>
            </li>
            <li class="breadcrumb-item active">Upgrade Requests</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="bx bx-up-arrow-alt me-2"></i>My Upgrade Requests
                            </h5>
                            <p class="text-muted mb-0">Track your service upgrade requests and their status</p>
                        </div>
                        <a href="{{ route('client.services.index') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>New Upgrade Request
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning bg-lighten">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-label-warning me-3">
                            <i class="bx bx-time fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $requests->where('status', 'pending')->count() }}</h4>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success bg-lighten">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-label-success me-3">
                            <i class="bx bx-check-circle fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $requests->where('status', 'approved')->count() }}</h4>
                            <small class="text-muted">Approved</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info bg-lighten">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-label-info me-3">
                            <i class="bx bx-cog fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $requests->where('status', 'processing')->count() }}</h4>
                            <small class="text-muted">Processing</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger bg-lighten">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-label-danger me-3">
                            <i class="bx bx-x-circle fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $requests->where('status', 'rejected')->count() }}</h4>
                            <small class="text-muted">Rejected</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Requests List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Requests</h5>
                </div>
                <div class="card-body">
                    @if($requests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Service</th>
                                        <th>Upgrade Details</th>
                                        <th>Price Difference</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $request)
                                        <tr>
                                            <td>
                                                <strong>#{{ $request->id }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $request->service->product }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $request->service->domain ?? 'N/A' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="text-start">
                                                        <small class="text-muted">From:</small><br>
                                                        <strong>{{ $request->current_plan }}</strong>
                                                    </div>
                                                    <i class="bx bx-right-arrow-alt mx-3 text-primary"></i>
                                                    <div class="text-start">
                                                        <small class="text-muted">To:</small><br>
                                                        <strong>{{ $request->requested_plan }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="{{ $request->price_difference >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $request->formatted_price_difference }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $request->status_badge_class }}">
                                                    {{ $request->status_text }}
                                                </span>
                                            </td>
                                            <td>
                                                <small>
                                                    {{ $request->created_at->format('M d, Y') }}
                                                    <br>
                                                    {{ $request->created_at->format('H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-horizontal"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('client.upgrade-requests.show', $request) }}">
                                                                <i class="bx bx-eye me-2"></i>View Details
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('client.services.manage', $request->service) }}">
                                                                <i class="bx bx-globe me-2"></i>Manage Service
                                                            </a>
                                                        </li>
                                                        @if($request->status === 'pending')
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#" onclick="cancelRequest({{ $request->id }})">
                                                                    <i class="bx bx-x me-2"></i>Cancel Request
                                                                </a>
                                                            </li>
                                                        @endif
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
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    <i class="bx bx-up-arrow-alt fs-2"></i>
                                </span>
                            </div>
                            <h5>No Upgrade Requests</h5>
                            <p class="text-muted mb-4">You haven't submitted any upgrade requests yet.</p>
                            <a href="{{ route('client.services.index') }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i>Browse Services
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cancelRequest(requestId) {
    if (confirm('Are you sure you want to cancel this upgrade request?')) {
        fetch(`/client/upgrade-requests/${requestId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Failed to cancel request', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error cancelling request: ' + error.message, 'danger');
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
