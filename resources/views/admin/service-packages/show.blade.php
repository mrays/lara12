@extends('layouts.admin')

@section('title', 'Service Package Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="tf-icons bx bx-package me-2"></i>Service Package Details
                        </h5>
                        <small class="text-muted">View package information and usage</small>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.service-packages.index') }}">Service Packages</a></li>
                            <li class="breadcrumb-item active">{{ $package->name }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Package Information -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="tf-icons bx bx-info-circle me-2"></i>Package Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Package Name:</label>
                            <p class="mb-0">{{ $package->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Base Price:</label>
                            <p class="mb-0 text-primary fw-bold fs-5">Rp {{ number_format($package->base_price, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status:</label>
                            <p class="mb-0">
                                @if($package->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Created:</label>
                            <p class="mb-0">{{ date('M d, Y H:i', strtotime($package->created_at)) }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Description:</label>
                            <div class="alert alert-light">
                                {{ $package->description }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Package Stats -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="tf-icons bx bx-bar-chart me-2"></i>Package Usage
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <h2 class="text-primary mb-0">{{ count($services) }}</h2>
                            <small class="text-muted">Active Services</small>
                        </div>
                        
                        @if(count($services) > 0)
                            @php
                                $totalRevenue = collect($services)->sum('price');
                            @endphp
                            <div class="mb-3">
                                <h4 class="text-success mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                                <small class="text-muted">Total Revenue</small>
                            </div>
                        @endif
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.service-packages.edit', $package->id) }}" class="btn btn-primary">
                                <i class="tf-icons bx bx-edit me-1"></i>Edit Package
                            </a>
                            
                            @if(count($services) == 0)
                                <button class="btn btn-outline-danger" onclick="deletePackage({{ $package->id }}, '{{ $package->name }}')">
                                    <i class="tf-icons bx bx-trash me-1"></i>Delete Package
                                </button>
                            @else
                                <button class="btn btn-outline-secondary" disabled title="Cannot delete package with active services">
                                    <i class="tf-icons bx bx-trash me-1"></i>Cannot Delete
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Free Domains Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="tf-icons bx bx-globe me-2"></i>Free Domains & Promotions
                    </h6>
                    <span class="badge bg-primary">{{ count($package->freeDomains) }} Domains</span>
                </div>
                <div class="card-body">
                    @if($package->freeDomains->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Domain Extension</th>
                                        <th>Duration</th>
                                        <th>Normal Price</th>
                                        <th>Discount</th>
                                        <th>Promo Price</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($package->freeDomains->sortBy('sort_order') as $freeDomain)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2">
                                                        <span class="avatar-initial rounded bg-label-primary">
                                                            <i class="bx bx-globe"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <strong>.{{ $freeDomain->domainExtension->extension }}</strong>
                                                        <br><small class="text-muted">{{ $freeDomain->domainExtension->formatted_price }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-info">{{ $freeDomain->duration_years }} Tahun</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">Rp {{ number_format($freeDomain->domainExtension->price, 0, ',', '.') }}</span>
                                            </td>
                                            <td>
                                                @if($freeDomain->is_free)
                                                    <span class="badge bg-success">100% OFF</span>
                                                @else
                                                    <span class="badge bg-warning">{{ $freeDomain->discount_percent }}% OFF</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($freeDomain->is_free)
                                                    <span class="text-success fw-bold">FREE</span>
                                                @else
                                                    <span class="text-primary fw-bold">
                                                        Rp {{ number_format($freeDomain->domainExtension->price * (1 - $freeDomain->discount_percent / 100), 0, ',', '.') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($freeDomain->is_free)
                                                    <span class="badge bg-success">
                                                        <i class="bx bx-gift me-1"></i>Gratis
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="bx bx-tag me-1"></i>Diskon
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="text-muted">
                                <i class="tf-icons bx bx-globe fs-1 mb-2"></i>
                                <p>No free domains or promotions configured for this package.</p>
                                <a href="{{ route('admin.service-packages.edit', $package->id) }}" class="btn btn-primary">
                                    <i class="tf-icons bx bx-edit me-1"></i>Add Domain Promotions
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Services Using This Package -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="tf-icons bx bx-list-ul me-2"></i>Services Using This Package
                    </h6>
                    <span class="badge bg-primary">{{ count($services) }} Services</span>
                </div>
                <div class="card-body">
                    @if(count($services) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Client</th>
                                        <th>Domain</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($services as $service)
                                        <tr>
                                            <td>{{ $service->id }}</td>
                                            <td>
                                                <strong>{{ $service->client_name ?? 'N/A' }}</strong>
                                            </td>
                                            <td>{{ $service->domain ?? '-' }}</td>
                                            <td>
                                                <span class="fw-bold text-primary">
                                                    Rp {{ number_format($service->price ?? 0, 0, ',', '.') }}
                                                </span>
                                                @if($service->price != $package->base_price)
                                                    <br><small class="text-muted">
                                                        (Base: Rp {{ number_format($package->base_price, 0, ',', '.') }})
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $service->status == 'Active' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $service->status }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $service->due_date ? date('M d, Y', strtotime($service->due_date)) : '-' }}
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('admin.services.show', $service->id) }}" 
                                                       class="btn btn-sm btn-outline-info" title="View Service">
                                                        <i class="tf-icons bx bx-show"></i>
                                                    </a>
                                                    <a href="{{ route('admin.services.edit', $service->id) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Edit Service">
                                                        <i class="tf-icons bx bx-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="text-muted">
                                <i class="tf-icons bx bx-package fs-1 mb-2"></i>
                                <p>No services are currently using this package.</p>
                                <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                                    <i class="tf-icons bx bx-plus me-1"></i>Create Service with This Package
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.service-packages.index') }}" class="btn btn-secondary">
                    <i class="tf-icons bx bx-arrow-back me-1"></i>Back to Packages
                </a>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.service-packages.edit', $package->id) }}" class="btn btn-primary">
                        <i class="tf-icons bx bx-edit me-1"></i>Edit Package
                    </a>
                    <button class="btn btn-outline-warning" onclick="togglePackageStatus({{ $package->id }}, {{ $package->is_active ? 'true' : 'false' }})">
                        <i class="tf-icons bx bx-toggle-{{ $package->is_active ? 'right' : 'left' }} me-1"></i>
                        {{ $package->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
