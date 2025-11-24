@extends('layouts.admin')

@section('title', 'Edit Service Package')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="tf-icons bx bx-edit me-2"></i>Edit Service Package
                        </h5>
                        <small class="text-muted">Update service package details</small>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.service-packages.index') }}">Service Packages</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.service-packages.update', $package->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Package Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <i class="tf-icons bx bx-package me-1"></i>Package Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $package->name) }}" 
                                       placeholder="e.g., Business Website Professional Type S"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Enter a descriptive name for the service package</small>
                            </div>

                            <!-- Base Price -->
                            <div class="col-md-6 mb-3">
                                <label for="base_price" class="form-label">
                                    <i class="tf-icons bx bx-money me-1"></i>Base Price (Rp) <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('base_price') is-invalid @enderror" 
                                       id="base_price" 
                                       name="base_price" 
                                       value="{{ old('base_price', $package->base_price) }}" 
                                       placeholder="4500000"
                                       min="0"
                                       step="1000"
                                       required>
                                @error('base_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Base price in Rupiah (can be customized per client)</small>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="tf-icons bx bx-detail me-1"></i>Package Description <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4" 
                                      placeholder="2 GB storage • 5 GB monthly traffic • 1 situs web • 1 email account GRATIS • Free domain • Free SSL & monitoring • Free akses login • Free biaya instalasi • Maksimal 2 revisi • Login cPanel tersedia."
                                      required>{{ old('description', $package->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Detailed description of features and specifications</small>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $package->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <i class="tf-icons bx bx-toggle-right me-1"></i>Active Package
                                </label>
                                <small class="form-text text-muted d-block">Only active packages can be assigned to clients</small>
                            </div>
                        </div>

                        <!-- Package Info -->
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="tf-icons bx bx-info-circle me-2"></i>Package Information
                            </h6>
                            <p class="mb-1"><strong>Package ID:</strong> {{ $package->id }}</p>
                            <p class="mb-1"><strong>Created:</strong> {{ date('M d, Y H:i', strtotime($package->created_at)) }}</p>
                            <p class="mb-0"><strong>Last Updated:</strong> {{ date('M d, Y H:i', strtotime($package->updated_at)) }}</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.service-packages.index') }}" class="btn btn-secondary">
                                <i class="tf-icons bx bx-arrow-back me-1"></i>Back to Packages
                            </a>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.service-packages.show', $package->id) }}" class="btn btn-outline-info">
                                    <i class="tf-icons bx bx-show me-1"></i>View Details
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="tf-icons bx bx-save me-1"></i>Update Package
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Format price input
document.getElementById('base_price').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value) {
        // Add thousand separators for display (optional)
        e.target.setAttribute('title', 'Rp ' + parseInt(value).toLocaleString('id-ID'));
    }
});

// Auto-resize textarea
document.getElementById('description').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});

// Initialize textarea height
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('description');
    textarea.style.height = 'auto';
    textarea.style.height = (textarea.scrollHeight) + 'px';
});
</script>
@endsection
