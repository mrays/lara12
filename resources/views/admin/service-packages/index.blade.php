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
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($packages as $package)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input row-checkbox cursor-pointer" value="{{ $package->id }}" onchange="updateSelectedCount()" style="width: 18px; height: 18px;">
                                        </td>
                                        <td>{{ $package->id }}</td>
                                        <td>
                                            <strong>{{ $package->name }}</strong>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 300px;" title="{{ $package->description }}">
                                                {{ Str::limit($package->description, 80) }}
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
                                            {{ $package->created_at ? date('M d, Y', strtotime($package->created_at)) : '-' }}
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <!-- View Package -->
                                                <a href="{{ route('admin.service-packages.show', $package->id) }}" 
                                                   class="btn btn-sm btn-outline-info" title="View Details">
                                                    <i class="tf-icons bx bx-show"></i>
                                                </a>
                                                
                                                <!-- Edit Package -->
                                                <a href="{{ route('admin.service-packages.edit', $package->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Edit Package">
                                                    <i class="tf-icons bx bx-edit"></i>
                                                </a>
                                                
                                                <!-- Toggle Status -->
                                                <button class="btn btn-sm btn-outline-warning" 
                                                        onclick="togglePackageStatus({{ $package->id }}, {{ $package->is_active ? 'true' : 'false' }})" 
                                                        title="{{ $package->is_active ? 'Deactivate' : 'Activate' }} Package">
                                                    <i class="tf-icons bx {{ $package->is_active ? 'bx-toggle-right' : 'bx-toggle-left' }}"></i>
                                                </button>
                                                
                                                <!-- Delete Package -->
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deletePackage({{ $package->id }}, '{{ $package->name }}')" 
                                                        title="Delete Package">
                                                    <i class="tf-icons bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
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
