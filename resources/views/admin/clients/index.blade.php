@extends('layouts.admin')

@section('title', 'Clients Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="bx bx-user me-2"></i>Clients Management
                        </h5>
                        <small class="text-muted">Manage all clients and their accounts</small>
                    </div>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newClientModal">
                            <i class="bx bx-plus me-1"></i>New Client
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mt-4">
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('vendor/sneat/assets/img/icons/unicons/chart-success.png') }}" alt="Total Clients" class="rounded">
                        </div>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#">View Details</a>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Total Clients</span>
                    <h3 class="card-title mb-2">{{ $clients->total() }}</h3>
                    <small class="text-success fw-semibold">
                        <i class="bx bx-up-arrow-alt"></i> Active Management
                    </small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('vendor/sneat/assets/img/icons/unicons/wallet-info.png') }}" alt="Active Clients" class="rounded">
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Active Clients</span>
                    <h3 class="card-title mb-2">{{ $clients->where('status', 'Active')->count() }}</h3>
                    <small class="text-success fw-semibold">
                        <i class="bx bx-check-circle"></i> Currently Active
                    </small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('vendor/sneat/assets/img/icons/unicons/paypal.png') }}" alt="New This Month" class="rounded">
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">New This Month</span>
                    <h3 class="card-title mb-2">{{ $clients->where('created_at', '>=', now()->startOfMonth())->count() }}</h3>
                    <small class="text-info fw-semibold">
                        <i class="bx bx-calendar"></i> Recent Additions
                    </small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('vendor/sneat/assets/img/icons/unicons/cc-primary.png') }}" alt="Services" class="rounded">
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Total Services</span>
                    <h3 class="card-title mb-2">{{ \DB::table('services')->count() }}</h3>
                    <small class="text-warning fw-semibold">
                        <i class="bx bx-package"></i> All Services
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Clients</h5>
                    <div class="d-flex gap-2">
                        <div class="input-group" style="width: 250px;">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search clients..." id="searchClients">
                        </div>
                        <select class="form-select" style="width: 150px;" id="filterStatus">
                            <option value="">All Status</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Client Info</th>
                                    <th>Contact</th>
                                    <th>Services</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $client)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary">#{{ $client->id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <img src="{{ asset('vendor/sneat/assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle">
                                            </div>
                                            <div>
                                                <h6 class="mb-0">
                                                    <a href="{{ route('admin.clients.show', $client) }}" class="text-decoration-none">
                                                        {{ $client->name }}
                                                    </a>
                                                </h6>
                                                <small class="text-muted">{{ $client->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <i class="bx bx-envelope me-1"></i>
                                            <small>{{ $client->email }}</small>
                                        </div>
                                        @if($client->phone)
                                        <div class="mt-1">
                                            <i class="bx bx-phone me-1"></i>
                                            <small>{{ $client->phone }}</small>
                                        </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary me-2">{{ \DB::table('services')->where('client_id', $client->id)->count() }}</span>
                                            <small class="text-muted">
                                                {{ \DB::table('services')->where('client_id', $client->id)->where('status', 'Active')->count() }} active
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        @switch($client->status ?? 'Active')
                                            @case('Active')
                                                <span class="badge bg-success">Active</span>
                                                @break
                                            @case('Inactive')
                                                <span class="badge bg-secondary">Inactive</span>
                                                @break
                                            @default
                                                <span class="badge bg-success">Active</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $client->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <!-- View Button -->
                                            <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-sm btn-outline-info" title="View Client">
                                                <i class="tf-icons bx bx-show"></i>
                                            </a>
                                            
                                            <!-- Edit Client Info Button -->
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="editClientInfo({{ $client->id }}, '{{ $client->name }}', '{{ $client->email }}', '{{ $client->phone ?? '' }}', '{{ $client->status ?? 'Active' }}')" 
                                                    title="Edit Client Info">
                                                <i class="tf-icons bx bx-edit"></i>
                                            </button>
                                            
                                            <!-- Manage Services Button -->
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="manageServices({{ $client->id }}, '{{ $client->name }}')" 
                                                    title="Manage Services">
                                                <i class="tf-icons bx bx-package"></i>
                                            </button>
                                            
                                            <!-- More Actions Dropdown -->
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" title="More Actions">
                                                    <i class="tf-icons bx bx-dots-horizontal"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <h6 class="dropdown-header">Account Actions</h6>
                                                    <button class="dropdown-item" onclick="resetPassword({{ $client->id }}, '{{ $client->name }}')">
                                                        <i class="tf-icons bx bx-key me-1"></i> Reset Password
                                                    </button>
                                                    <button class="dropdown-item" onclick="toggleStatus({{ $client->id }}, '{{ $client->status ?? 'Active' }}')">
                                                        <i class="tf-icons bx bx-toggle-left me-1"></i> Toggle Status
                                                    </button>
                                                    <div class="dropdown-divider"></div>
                                                    <button class="dropdown-item text-danger" onclick="deleteClient({{ $client->id }}, '{{ $client->name }}')">
                                                        <i class="tf-icons bx bx-trash me-1"></i> Delete Client
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <img src="{{ asset('vendor/sneat/assets/img/illustrations/page-misc-error-light.png') }}" alt="No clients" width="150">
                                        <p class="mt-3 text-muted">No clients found</p>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newClientModal">
                                            <i class="bx bx-plus me-1"></i>Add First Client
                                        </button>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($clients->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <small class="text-muted">
                                Showing {{ $clients->firstItem() }} to {{ $clients->lastItem() }} of {{ $clients->total() }} results
                            </small>
                        </div>
                        <div>
                            {{ $clients->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Client Modal -->
<div class="modal fade" id="newClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-user-plus me-2"></i>Add New Client
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.clients.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password')">
                                    <i class="bx bx-show" id="password-icon"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimum 8 characters</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes about this client..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>Create Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-key me-2"></i>Reset Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="resetPasswordForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>
                        You are about to reset password for: <strong id="resetClientName"></strong>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('new_password')">
                                <i class="bx bx-show" id="new_password-icon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="password_confirmation" required>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notify_client">
                        <label class="form-check-label" for="notify_client">
                            Notify client via email about password change
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bx bx-key me-1"></i>Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Client Info Modal -->
<div class="modal fade" id="editClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="tf-icons bx bx-edit me-2"></i>Edit Client Information
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editClientForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="edit_phone" name="phone" placeholder="+62xxx">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="tf-icons bx bx-save me-1"></i>Update Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manage Services Modal -->
<div class="modal fade" id="manageServicesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="tf-icons bx bx-package me-2"></i>Manage Services for <span id="manageServicesClientName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="manageServicesForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="service_type" class="form-label">Service Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="service_type" name="service_type" required>
                                <option value="">-- Choose Service --</option>
                                <option value="Website">Website</option>
                                <option value="Mobile App">Mobile App</option>
                                <option value="SEO">SEO</option>
                                <option value="Hosting">Hosting</option>
                                <option value="Domain">Domain</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="service_price" class="form-label">Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="service_price" name="price" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="service_status" class="form-label">Status</label>
                            <select class="form-select" id="service_status" name="status">
                                <option value="Active">Active</option>
                                <option value="Pending">Pending</option>
                                <option value="Suspended">Suspended</option>
                                <option value="Terminated">Terminated</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="service_description" class="form-label">Description</label>
                        <textarea class="form-control" id="service_description" name="description" rows="3" placeholder="Service description..."></textarea>
                    </div>
                    
                    <!-- Existing Services List -->
                    <div class="mt-4">
                        <h6>Current Services</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="currentServicesList">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Loading services...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">
                        <i class="tf-icons bx bx-plus me-1"></i>Add Service
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bx bx-hide';
    } else {
        input.type = 'password';
        icon.className = 'bx bx-show';
    }
}

// Reset password function
function resetPassword(clientId, clientName) {
    document.getElementById('resetClientName').textContent = clientName;
    document.getElementById('resetPasswordForm').action = `/admin/clients/${clientId}/reset-password`;
    new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
}

// Edit client info function
function editClientInfo(clientId, name, email, phone, status) {
    document.getElementById('editClientForm').action = `/admin/clients/${clientId}`;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_status').value = status;
    
    new bootstrap.Modal(document.getElementById('editClientModal')).show();
}

// Manage services function
function manageServices(clientId, clientName) {
    document.getElementById('manageServicesClientName').textContent = clientName;
    document.getElementById('manageServicesForm').action = `/admin/clients/${clientId}/services`;
    
    // Load existing services for this client
    loadClientServices(clientId);
    
    new bootstrap.Modal(document.getElementById('manageServicesModal')).show();
}

// Toggle status function
function toggleStatus(clientId, currentStatus) {
    const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';
    if (confirm(`Change client status to ${newStatus}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/clients/${clientId}/toggle-status`;
        form.innerHTML = `
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="${newStatus}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Load client services
function loadClientServices(clientId) {
    // Clear the loading message and show actual services
    const tbody = document.getElementById('currentServicesList');
    
    // For now, show a message that services will be loaded
    // In a real implementation, this would be an AJAX call
    tbody.innerHTML = `
        <tr>
            <td colspan="4" class="text-center text-muted py-3">
                <i class="tf-icons bx bx-info-circle me-1"></i>
                Services will be loaded here. Click "Add Service" to create new services.
            </td>
        </tr>
    `;
}

// Delete client function
function deleteClient(clientId, clientName) {
    if (confirm(`Are you sure you want to delete client "${clientName}"? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/clients/${clientId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Search functionality
document.getElementById('searchClients').addEventListener('input', function() {
    // Add search functionality here
    console.log('Search:', this.value);
});

// Filter functionality
document.getElementById('filterStatus').addEventListener('change', function() {
    // Add filter functionality here
    console.log('Filter:', this.value);
});
</script>
@endsection
