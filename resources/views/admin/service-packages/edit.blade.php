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
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="bx bx-error-circle me-2"></i>Validation Errors:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.service-packages.update', $package->id) }}" method="POST" id="packageForm">
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
                                    @if($package->features && count($package->features) > 0)
                                        @foreach($package->features as $key => $value)
                                            <div class="feature-row mb-3">
                                                <div class="row align-items-end">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Feature Name</label>
                                                        <input type="text" class="form-control" name="features[{{ $loop->index }}][name]" value="{{ $key }}" placeholder="e.g., storage, websites, email_accounts">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Value</label>
                                                        <input type="text" class="form-control" name="features[{{ $loop->index }}][value]" value="{{ is_bool($value) ? ($value ? 'true' : 'false') : $value }}" placeholder="e.g., 2 GB, 1, true">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Type</label>
                                                        <select class="form-select" name="features[{{ $loop->index }}][type]">
                                                            <option value="text" {{ !is_bool($value) ? 'selected' : '' }}>Text</option>
                                                            <option value="boolean" {{ is_bool($value) ? 'selected' : '' }}>Boolean</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeFeatureRow(this)">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
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
                                    @endif
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
                                       {{ old('is_active', $package->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <i class="tf-icons bx bx-toggle-right me-1"></i>Active Package
                                </label>
                                <small class="form-text text-muted d-block">Only active packages can be assigned to clients</small>
                            </div>
                        </div>

                        <!-- Domain Include Promo -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="tf-icons bx bx-globe me-2"></i>Domain Include Promo
                                </h6>
                                <button type="button" class="btn btn-sm btn-primary" onclick="addDomainRow()">
                                    <i class="bx bx-plus me-1"></i>Tambah Domain
                                </button>
                            </div>
                            <div class="card-body">
                                @error('free_domains')
                                    <div class="alert alert-danger">
                                        <i class="bx bx-error-circle me-2"></i>{{ $message }}
                                    </div>
                                @enderror
                                
                                <div id="domainRowsContainer">
                                    <!-- Existing domains will be loaded here -->
                                    @foreach($package->freeDomains as $index => $freeDomain)
                                        @include('admin.service-packages.partials.domain-row', [
                                            'index' => $index, 
                                            'freeDomain' => $freeDomain,
                                            'groupedDomains' => $groupedDomains
                                        ])
                                    @endforeach
                                    
                                    <!-- If no existing domains, show one empty row -->
                                    @if($package->freeDomains->isEmpty())
                                        @include('admin.service-packages.partials.domain-row', [
                                            'index' => 0, 
                                            'freeDomain' => null,
                                            'groupedDomains' => $groupedDomains
                                        ])
                                    @endif
                                </div>

                                <!-- Domain Preview -->
                                <div class="mt-3" id="domainPreviewContainer">
                                    <!-- Previews will be shown here -->
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
                                <button type="submit" class="btn btn-primary" id="updatePackageBtn">
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

<!-- Hidden templates for JavaScript -->
<div style="display: none;">
    <div id="domainOptionsTemplate">
        @foreach($groupedDomains as $extension => $domains)
            <optgroup label=".{{ $extension }}">
                @foreach($domains as $domain)
                    <option value="{{ $domain->id }}" 
                            data-price="{{ $domain->price }}"
                            data-duration="{{ $domain->duration_years }}"
                            data-extension="{{ $domain->extension }}">
                        .{{ $domain->extension }} ({{ $domain->duration_years }} tahun) - {{ $domain->formatted_price }}
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    </div>
    
    <div id="durationOptionsTemplate">
        @for($i = 1; $i <= 10; $i++)
            <option value="{{ $i }}">{{ $i }}</option>
        @endfor
    </div>
</div>

<script>
let domainRowCount = {{ max($package->freeDomains->count(), 1) }};

// Format price input
document.getElementById('base_price').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value) {
        e.target.setAttribute('title', 'Rp ' + parseInt(value).toLocaleString('id-ID'));
    }
});

// Auto-resize textarea
document.getElementById('description').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});

// Add new domain row
function addDomainRow() {
    const container = document.getElementById('domainRowsContainer');
    const domainOptions = document.getElementById('domainOptionsTemplate').innerHTML;
    const durationOptions = document.getElementById('durationOptionsTemplate').innerHTML;
    
    const template = `
        <div class="domain-row border rounded p-3 mb-3" data-index="${domainRowCount}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">
                    <i class="bx bx-globe me-1"></i>Domain #${domainRowCount + 1}
                </h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeDomainRow(${domainRowCount})">
                    <i class="bx bx-trash me-1"></i>Hapus
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Domain Extension</label>
                    <select class="form-select domain-extension-select" 
                            name="free_domains[${domainRowCount}][domain_extension_id]"
                            onchange="updateDomainPreview(${domainRowCount})">
                        <option value="">Pilih Domain</option>
                        ${domainOptions}
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">Durasi (Tahun)</label>
                    <select class="form-select domain-duration-select" 
                            name="free_domains[${domainRowCount}][duration_years]"
                            onchange="updateDomainPreview(${domainRowCount})">
                        <option value="">Pilih</option>
                        ${durationOptions}
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">Diskon (%)</label>
                    <input type="number" 
                           class="form-control domain-discount-input" 
                           name="free_domains[${domainRowCount}][discount_percent]"
                           value="0"
                           min="0" 
                           max="100" 
                           step="0.01"
                           oninput="updateDomainPreview(${domainRowCount})">
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">Harga Promo</label>
                    <div class="form-control-plaintext">
                        <span class="domain-price-display" id="domainPriceDisplay${domainRowCount}">-</span>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Status</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input domain-free-checkbox" 
                               type="checkbox" 
                               name="free_domains[${domainRowCount}][is_free]" 
                               value="1"
                               onchange="toggleFreeDomain(${domainRowCount})">
                        <label class="form-check-label">
                            <i class="bx bx-gift me-1"></i>Domain Gratis
                        </label>
                    </div>
                </div>
            </div>

            <div class="domain-preview" id="domainPreview${domainRowCount}" style="display: none;">
                <div class="alert alert-success alert-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Domain:</strong> <span class="preview-domain">-</span><br>
                            <strong>Durasi:</strong> <span class="preview-duration">-</span><br>
                            <strong>Harga Normal:</strong> <span class="preview-normal-price">-</span><br>
                            <strong>Diskon:</strong> <span class="preview-discount">-</span><br>
                            <strong>Harga Promo:</strong> <span class="preview-promo-price">-</span>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success preview-status">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', template);
    domainRowCount++;
}

// Remove domain row
function removeDomainRow(index) {
    const row = document.querySelector(`.domain-row[data-index="${index}"]`);
    if (row) {
        row.remove();
        updateAllDomainPreviews();
    }
}

// Update domain preview for specific row
function updateDomainPreview(index) {
    const row = document.querySelector(`.domain-row[data-index="${index}"]`);
    if (!row) return;
    
    const domainSelect = row.querySelector('.domain-extension-select');
    const durationSelect = row.querySelector('.domain-duration-select');
    const discountInput = row.querySelector('.domain-discount-input');
    const isFreeCheckbox = row.querySelector('.domain-free-checkbox');
    const previewDiv = row.querySelector('.domain-preview');
    const priceDisplay = document.getElementById(`domainPriceDisplay${index}`);
    
    // Check for duplicate domains
    checkDuplicateDomains();
    
    if (domainSelect.value && durationSelect.value) {
        const selectedOption = domainSelect.options[domainSelect.selectedIndex];
        const extension = selectedOption.dataset.extension;
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
        row.querySelector('.preview-domain').textContent = '.' + extension;
        row.querySelector('.preview-duration').textContent = duration + ' Tahun';
        row.querySelector('.preview-normal-price').textContent = 'Rp ' + normalPrice.toLocaleString('id-ID');
        row.querySelector('.preview-discount').textContent = discount + '%';
        row.querySelector('.preview-promo-price').textContent = promoPrice === 0 ? 'FREE' : 'Rp ' + promoPrice.toLocaleString('id-ID');
        row.querySelector('.preview-status').textContent = isFreeCheckbox.checked ? 'GRATIS' : 'DISKON';
        
        // Update price display
        priceDisplay.textContent = promoPrice === 0 ? 'FREE' : 'Rp ' + promoPrice.toLocaleString('id-ID');
        
        previewDiv.style.display = 'block';
    } else {
        previewDiv.style.display = 'none';
        priceDisplay.textContent = '-';
    }
}

// Check for duplicate domain selections
function checkDuplicateDomains() {
    const domainSelects = document.querySelectorAll('.domain-extension-select');
    const selectedDomains = [];
    const duplicates = new Set();
    
    domainSelects.forEach((select, index) => {
        const value = select.value;
        if (value) {
            if (selectedDomains.includes(value)) {
                duplicates.add(value);
            }
            selectedDomains.push(value);
        }
        
        // Reset validation state
        select.classList.remove('is-invalid');
    });
    
    // Mark duplicates as invalid
    domainSelects.forEach(select => {
        if (duplicates.has(select.value)) {
            select.classList.add('is-invalid');
        }
    });
    
    // Show/hide duplicate warning
    const existingError = document.querySelector('.domain-duplicate-warning');
    if (existingError) {
        existingError.remove();
    }
    
    if (duplicates.size > 0) {
        const warning = document.createElement('div');
        warning.className = 'alert alert-warning domain-duplicate-warning';
        warning.innerHTML = '<i class="bx bx-error-circle me-2"></i>Domain yang sama telah dipilih lebih dari satu kali. Silakan pilih domain yang berbeda untuk setiap baris.';
        document.getElementById('domainRowsContainer').insertAdjacentElement('afterbegin', warning);
    }
}

// Toggle free domain for specific row
function toggleFreeDomain(index) {
    const row = document.querySelector(`.domain-row[data-index="${index}"]`);
    if (!row) return;
    
    const discountInput = row.querySelector('.domain-discount-input');
    const isFreeCheckbox = row.querySelector('.domain-free-checkbox');
    
    if (isFreeCheckbox.checked) {
        discountInput.value = 100;
        discountInput.disabled = true;
    } else {
        discountInput.disabled = false;
        discountInput.value = 0;
    }
    updateDomainPreview(index);
}

// Update all domain previews (useful after removing rows)
function updateAllDomainPreviews() {
    document.querySelectorAll('.domain-row').forEach(row => {
        const index = parseInt(row.dataset.index);
        updateDomainPreview(index);
    });
}

// Features management functions
let featureIndex = {{ $package->features ? count($package->features) : 1 }};

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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('description');
    textarea.style.height = 'auto';
    textarea.style.height = (textarea.scrollHeight) + 'px';
    
    // Initialize all existing domain previews
    document.querySelectorAll('.domain-row').forEach(row => {
        const index = parseInt(row.dataset.index);
        updateDomainPreview(index);
        
        // Disable discount input if domain is free
        const isFreeCheckbox = row.querySelector('.domain-free-checkbox');
        const discountInput = row.querySelector('.domain-discount-input');
        if (isFreeCheckbox && isFreeCheckbox.checked) {
            discountInput.disabled = true;
        }
    });

    // Form submit handler
    const form = document.getElementById('packageForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submitting...');
            
            // Show loading state on submit button
            const submitBtn = document.getElementById('updatePackageBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="tf-icons bx bx-loader-alt bx-spin me-1"></i>Updating...';
            }
            
            // Remove empty feature rows before submit
            const featureRows = document.querySelectorAll('.feature-row');
            featureRows.forEach(row => {
                const nameInput = row.querySelector('input[name*="[name]"]');
                const valueInput = row.querySelector('input[name*="[value]"]');
                
                if (nameInput && valueInput && (!nameInput.value.trim() || !valueInput.value.trim())) {
                    // Remove empty inputs to avoid validation errors
                    nameInput.remove();
                    valueInput.remove();
                    row.querySelector('select[name*="[type]"]')?.remove();
                }
            });
        });
    }
});
</script>
@endsection
