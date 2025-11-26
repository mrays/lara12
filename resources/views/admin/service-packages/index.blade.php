@extends('layouts.admin')

@section('title', 'Service Packages')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="tf-icons bx bx-package me-2"></i>Service Packages
                        </h5>
                        <small class="text-muted">Manage service packages that can be assigned to clients</small>
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <button type="button" class="btn btn-danger btn-sm" id="deleteSelectedBtn" style="display: none;" onclick="deleteSelected()">
                            <i class="bx bx-trash"></i>
                            <span class="d-none d-md-inline ms-1">Delete Selected</span>
                            (<span id="selectedCount">0</span>)
                        </button>
                        <a href="{{ route('admin.service-packages.create') }}" class="btn btn-primary btn-sm">
                            <i class="tf-icons bx bx-plus"></i>
                            <span class="d-none d-sm-inline ms-1">New Package</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="tf-icons bx bx-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="tf-icons bx bx-error-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Service Packages Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="45" class="text-center">
                                        <input type="checkbox" class="form-check-input cursor-pointer" id="selectAll" onclick="toggleSelectAll()" style="width: 18px; height: 18px;">
                                    </th>
                                    <th>#</th>
                                    <th>Package Name</th>
                                    <th>Description</th>
                                    <th>Base Price</th>
                                    <th>Status</th>
                                    <th>Visibility</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($packages as $package)
                                    <tr class="{{ $package->is_custom ? 'table-warning' : '' }}">
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input row-checkbox cursor-pointer" value="{{ $package->id }}" onchange="updateSelectedCount()" style="width: 18px; height: 18px;">
                                        </td>
                                        <td>{{ $package->id }}</td>
                                        <td>
                                            <strong>{{ $package->name }}</strong>
                                            @if($package->is_custom)
                                                <span class="badge bg-warning text-dark ms-1" title="Custom package for specific clients">
                                                    <i class="bx bx-star"></i>
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 250px;" title="{{ $package->description }}">
                                                {{ Str::limit($package->description, 60) }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-primary">
                                                Rp {{ number_format($package->base_price, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($package->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($package->is_visible)
                                                <span class="badge bg-info" title="Visible on client order page">
                                                    <i class="bx bx-show"></i> Visible
                                                </span>
                                            @else
                                                <span class="badge bg-dark" title="Hidden from client order page">
                                                    <i class="bx bx-hide"></i> Hidden
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($package->is_custom)
                                                <span class="badge bg-warning text-dark" title="Custom package for specific clients only">
                                                    <i class="bx bx-star"></i> Custom
                                                </span>
                                            @else
                                                <span class="badge bg-primary" title="Standard package for all clients">
                                                    <i class="bx bx-package"></i> Standard
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.service-packages.show', $package->id) }}">
                                                            <i class="bx bx-show me-2"></i>View Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.service-packages.edit', $package->id) }}">
                                                            <i class="bx bx-edit me-2"></i>Edit Package
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" onclick="togglePackageStatus({{ $package->id }}, {{ $package->is_active ? 'true' : 'false' }}); return false;">
                                                            <i class="bx {{ $package->is_active ? 'bx-x-circle text-warning' : 'bx-check-circle text-success' }} me-2"></i>
                                                            {{ $package->is_active ? 'Deactivate' : 'Activate' }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" onclick="toggleVisibility({{ $package->id }}, {{ $package->is_visible ? 'true' : 'false' }}); return false;">
                                                            <i class="bx {{ $package->is_visible ? 'bx-hide text-dark' : 'bx-show text-info' }} me-2"></i>
                                                            {{ $package->is_visible ? 'Hide from Order Page' : 'Show on Order Page' }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" onclick="toggleCustom({{ $package->id }}, {{ $package->is_custom ? 'true' : 'false' }}); return false;">
                                                            <i class="bx {{ $package->is_custom ? 'bx-package text-primary' : 'bx-star text-warning' }} me-2"></i>
                                                            {{ $package->is_custom ? 'Mark as Standard' : 'Mark as Custom' }}
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="deletePackage({{ $package->id }}, '{{ $package->name }}'); return false;">
                                                            <i class="bx bx-trash me-2"></i>Delete Package
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="tf-icons bx bx-package fs-1 mb-2"></i>
                                                <p>No service packages found.</p>
                                                <a href="{{ route('admin.service-packages.create') }}" class="btn btn-primary">
                                                    <i class="tf-icons bx bx-plus me-1"></i>Create First Package
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle select all checkboxes
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateSelectedCount();
}

// Update selected count and show/hide delete button
function updateSelectedCount() {
    const checked = document.querySelectorAll('.row-checkbox:checked');
    const count = checked.length;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('deleteSelectedBtn').style.display = count > 0 ? 'inline-block' : 'none';
}

// Delete selected items
function deleteSelected() {
    const checked = document.querySelectorAll('.row-checkbox:checked');
    if (checked.length === 0) return;
    
    if (confirm(`Are you sure you want to delete ${checked.length} package(s)? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.service-packages.bulk-delete") }}';
        form.innerHTML = `@csrf`;
        
        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Toggle package status
function togglePackageStatus(packageId, currentStatus) {
    const action = currentStatus ? 'deactivate' : 'activate';
    if (confirm(`Are you sure you want to ${action} this service package?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/service-packages/${packageId}/toggle-status`;
        form.innerHTML = `
            @csrf
            @method('PUT')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Toggle package visibility
function toggleVisibility(packageId, currentVisibility) {
    const action = currentVisibility ? 'hide' : 'show';
    if (confirm(`Are you sure you want to ${action} this package on the client order page?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/service-packages/${packageId}/toggle-visibility`;
        form.innerHTML = `
            @csrf
            @method('PUT')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Toggle package custom status
function toggleCustom(packageId, isCustom) {
    const action = isCustom ? 'standard' : 'custom';
    const message = isCustom 
        ? 'Mark this package as standard? It will be available for all clients.'
        : 'Mark this package as custom? It will only be assignable manually to specific clients.';
    
    if (confirm(message)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/service-packages/${packageId}/toggle-custom`;
        form.innerHTML = `
            @csrf
            @method('PUT')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Delete package
function deletePackage(packageId, packageName) {
    if (confirm(`Are you sure you want to delete "${packageName}"? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/service-packages/${packageId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
