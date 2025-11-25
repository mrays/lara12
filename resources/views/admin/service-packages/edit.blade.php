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

                        <!-- Domain Include Promo -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="tf-icons bx bx-globe me-2"></i>Domain Include Promo
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="domain_extension_id" class="form-label">
                                            Domain Extension
                                        </label>
                                        <select class="form-select @error('domain_extension_id') is-invalid @enderror" 
                                                id="domain_extension_id" 
                                                name="domain_extension_id">
                                            <option value="">Tidak ada domain</option>
                                            @foreach($groupedDomains as $extension => $domains)
                                                <optgroup label=".{{ $extension }}">
                                                    @foreach($domains as $domain)
                                                        <option value="{{ $domain->id }}" 
                                                                {{ old('domain_extension_id', $package->domain_extension_id) == $domain->id ? 'selected' : '' }}
                                                                data-price="{{ $domain->price }}"
                                                                data-duration="{{ $domain->duration_years }}">
                                                            .{{ $domain->extension }} ({{ $domain->duration_years }} tahun) - {{ $domain->formatted_price }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                        @error('domain_extension_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Pilih domain yang termasuk dalam paket promo</small>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="domain_duration_years" class="form-label">
                                            Durasi Domain
                                        </label>
                                        <select class="form-select @error('domain_duration_years') is-invalid @enderror" 
                                                id="domain_duration_years" 
                                                name="domain_duration_years">
                                            <option value="">Pilih durasi</option>
                                            @for($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}" {{ old('domain_duration_years', $package->domain_duration_years) == $i ? 'selected' : '' }}>
                                                    {{ $i }} Tahun
                                                </option>
                                            @endfor
                                        </select>
                                        @error('domain_duration_years')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="domain_discount_percent" class="form-label">
                                            Diskon Domain (%)
                                        </label>
                                        <input type="number" 
                                               class="form-control @error('domain_discount_percent') is-invalid @enderror" 
                                               id="domain_discount_percent" 
                                               name="domain_discount_percent" 
                                               value="{{ old('domain_discount_percent', $package->domain_discount_percent) }}" 
                                               min="0" 
                                               max="100" 
                                               step="0.01">
                                        @error('domain_discount_percent')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">100% = Gratis domain</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="is_domain_free" 
                                                   name="is_domain_free" 
                                                   value="1" 
                                                   {{ old('is_domain_free', $package->is_domain_free) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_domain_free">
                                                <i class="tf-icons bx bx-gift me-1"></i>Domain Gratis
                                            </label>
                                            <small class="form-text text-muted d-block">Centang jika domain termasuk gratis dalam paket</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Domain Preview -->
                                <div class="mt-3" id="domainPreview" style="display: none;">
                                    <div class="alert alert-success">
                                        <h6 class="alert-heading">
                                            <i class="tf-icons bx bx-check-circle me-2"></i>Domain Promo Preview
                                        </h6>
                                        <p class="mb-0">
                                            <strong>Domain:</strong> <span id="previewDomain">-</span><br>
                                            <strong>Durasi:</strong> <span id="previewDuration">-</span><br>
                                            <strong>Harga Normal:</strong> <span id="previewNormalPrice">-</span><br>
                                            <strong>Diskon:</strong> <span id="previewDiscount">-</span><br>
                                            <strong>Harga Promo:</strong> <span id="previewPromoPrice">-</span>
                                        </p>
                                    </div>
                                </div>
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

// Domain preview functionality
function updateDomainPreview() {
    const domainSelect = document.getElementById('domain_extension_id');
    const durationSelect = document.getElementById('domain_duration_years');
    const discountInput = document.getElementById('domain_discount_percent');
    const isFreeCheckbox = document.getElementById('is_domain_free');
    const previewDiv = document.getElementById('domainPreview');
    
    if (domainSelect.value && durationSelect.value) {
        const selectedOption = domainSelect.options[domainSelect.selectedIndex];
        const domainText = selectedOption.text;
        const normalPrice = parseFloat(selectedOption.dataset.price);
        const duration = durationSelect.value;
        let discount = parseFloat(discountInput.value) || 0;
        
        // Auto-set discount to 100% if free checkbox is checked
        if (isFreeCheckbox.checked) {
            discount = 100;
            discountInput.value = 100;
        }
        
        // Calculate promo price
        let promoPrice = normalPrice;
        if (discount > 0) {
            promoPrice = normalPrice * (1 - discount / 100);
        }
        
        // Update preview
        document.getElementById('previewDomain').textContent = domainText.split(' - ')[0];
        document.getElementById('previewDuration').textContent = duration + ' Tahun';
        document.getElementById('previewNormalPrice').textContent = 'Rp ' + normalPrice.toLocaleString('id-ID');
        document.getElementById('previewDiscount').textContent = discount + '%';
        document.getElementById('previewPromoPrice').textContent = promoPrice === 0 ? 'FREE' : 'Rp ' + promoPrice.toLocaleString('id-ID');
        
        previewDiv.style.display = 'block';
    } else {
        previewDiv.style.display = 'none';
    }
}

// Event listeners for domain fields
document.getElementById('domain_extension_id').addEventListener('change', updateDomainPreview);
document.getElementById('domain_duration_years').addEventListener('change', updateDomainPreview);
document.getElementById('domain_discount_percent').addEventListener('input', updateDomainPreview);

// Handle free domain checkbox
document.getElementById('is_domain_free').addEventListener('change', function() {
    const discountInput = document.getElementById('domain_discount_percent');
    if (this.checked) {
        discountInput.value = 100;
        discountInput.disabled = true;
    } else {
        discountInput.disabled = false;
        discountInput.value = 0;
    }
    updateDomainPreview();
});

// Initialize textarea height
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('description');
    textarea.style.height = 'auto';
    textarea.style.height = (textarea.scrollHeight) + 'px';
    
    // Initialize domain preview if values exist
    updateDomainPreview();
    
    // Disable discount input if domain is free
    if (document.getElementById('is_domain_free').checked) {
        document.getElementById('domain_discount_percent').disabled = true;
    }
});
</script>
@endsection
