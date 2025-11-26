@extends('layouts.admin')

@section('title', 'Create Service Package')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="tf-icons bx bx-plus me-2"></i>Create New Service Package
                        </h5>
                        <small class="text-muted">Add a new service package that can be assigned to clients</small>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.service-packages.index') }}">Service Packages</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.service-packages.store') }}" method="POST">
                        @csrf
                        
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
                                       value="{{ old('name') }}" 
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
                                       value="{{ old('base_price') }}" 
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
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Detailed description of features and specifications</small>
                        </div>

                        <!-- Package Features -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="tf-icons bx bx-check-square me-2"></i>Package Features
                                </h6>
                                <button type="button" class="btn btn-sm btn-primary" onclick="addFeatureRow()">
                                    <i class="bx bx-plus me-1"></i>Add Feature
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="featuresContainer">
                                    <div class="feature-row mb-3">
                                        <div class="row align-items-end">
                                            <div class="col-md-4">
                                                <label class="form-label">Feature Name</label>
                                                <input type="text" class="form-control" name="features[0][name]" placeholder="e.g., storage">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Value</label>
                                                <input type="text" class="form-control" name="features[0][value]" placeholder="e.g., 2 GB">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Type</label>
                                                <select class="form-select" name="features[0][type]">
                                                    <option value="text">Text</option>
                                                    <option value="boolean">Boolean</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeFeatureRow(this)">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info mt-3">
                                    <small>
                                        <strong>Feature Examples:</strong><br>
                                        • <strong>storage:</strong> 2 GB (text)<br>
                                        • <strong>websites:</strong> 1 (text)<br>
                                        • <strong>email_accounts:</strong> 1 (text)<br>
                                        • <strong>cpanel:</strong> true (boolean)<br>
                                        • <strong>ssl:</strong> true (boolean)<br>
                                        • <strong>domain:</strong> true (boolean)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <i class="tf-icons bx bx-toggle-right me-1"></i>Active Package
                                </label>
                                <small class="form-text text-muted d-block">Only active packages can be assigned to clients</small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.service-packages.index') }}" class="btn btn-secondary">
                                <i class="tf-icons bx bx-arrow-back me-1"></i>Back to Packages
                            </a>
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="tf-icons bx bx-reset me-1"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="tf-icons bx bx-save me-1"></i>Create Package
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Package Examples -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="tf-icons bx bx-info-circle me-2"></i>Package Examples
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Business Website Exclusive Type S</h6>
                            <p class="small text-muted mb-2">
                                2 GB storage • 5 GB monthly traffic • 1 situs web • 1 email account GRATIS • Free domain • Free SSL & monitoring • Free akses login • Free biaya instalasi • Maksimal 2 revisi • Login cPanel tersedia.
                            </p>
                            <p class="fw-bold text-success">Rp 4,500,000</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Business Website Professional Type M</h6>
                            <p class="small text-muted mb-2">
                                3,5 GB storage • Unlimited monthly traffic • "No Limit Sub Features" • 1 situs web • 2 "Email Account Pro" • Free domain • Free SSL & monitoring • Free akses login • Free biaya instalasi • Maksimal 3 revisi • Login cPanel tersedia.
                            </p>
                            <p class="fw-bold text-success">Rp 7,580,000</p>
                        </div>
                    </div>
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

// Features management functions
let featureIndex = 1;

function addFeatureRow() {
    const container = document.getElementById('featuresContainer');
    const newRow = document.createElement('div');
    newRow.className = 'feature-row mb-3';
    newRow.innerHTML = `
        <div class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label">Feature Name</label>
                <input type="text" class="form-control" name="features[${featureIndex}][name]" placeholder="e.g., storage, websites, email_accounts">
            </div>
            <div class="col-md-4">
                <label class="form-label">Value</label>
                <input type="text" class="form-control" name="features[${featureIndex}][value]" placeholder="e.g., 2 GB, 1, true">
            </div>
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select class="form-select" name="features[${featureIndex}][type]">
                    <option value="text">Text</option>
                    <option value="boolean">Boolean</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeFeatureRow(this)">
                    <i class="bx bx-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newRow);
    featureIndex++;
}

function removeFeatureRow(button) {
    const row = button.closest('.feature-row');
    if (document.querySelectorAll('.feature-row').length > 1) {
        row.remove();
    } else {
        // Clear the inputs instead of removing if it's the last row
        row.querySelectorAll('input').forEach(input => input.value = '');
        row.querySelector('select').selectedIndex = 0;
    }
}
</script>
@endsection
