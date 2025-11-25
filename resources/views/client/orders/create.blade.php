@extends('layouts.sneat-dashboard')

@section('title', 'Order New Service')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('client.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Order New Service</li>
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
                                <i class="bx bx-cart me-2"></i>Order New Service
                            </h5>
                            <p class="text-muted mb-0">Pilih paket layanan yang sesuai dengan kebutuhan Anda</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('client.orders.store') }}" method="POST" id="orderForm">
        @csrf
        
        <div class="row">
            <!-- Package Selection -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bx bx-package me-2"></i>Pilih Paket Layanan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            @forelse($packages as $index => $package)
                            <div class="col-md-4">
                                <div class="card border h-100 package-card {{ $index === 1 ? 'border-primary' : '' }}" 
                                     data-package-id="{{ $package->id }}"
                                     data-package-name="{{ $package->name }}"
                                     data-base-price="{{ $package->base_price }}"
                                     data-domain-promo="{{ $package->domainExtension ? 'true' : 'false' }}"
                                     data-domain-extension-id="{{ $package->domain_extension_id ?? '' }}"
                                     data-domain-duration="{{ $package->domain_duration_years ?? '' }}"
                                     data-domain-discount="{{ $package->domain_discount_percent ?? 0 }}"
                                     data-domain-free="{{ $package->is_domain_free ? 'true' : 'false' }}"
                                     onclick="selectPackage({{ $package->id }})">
                                    
                                    @if($index === 1)
                                        <div class="position-absolute top-0 start-50 translate-middle">
                                            <span class="badge bg-primary">Popular</span>
                                        </div>
                                    @endif
                                    
                                    <div class="card-body text-center p-4">
                                        <!-- Package Icon -->
                                        <div class="mb-3">
                                            <div class="avatar avatar-lg mx-auto">
                                                <span class="avatar-initial rounded-circle bg-label-{{ $index === 0 ? 'primary' : ($index === 1 ? 'success' : 'info') }}">
                                                    <i class="bx {{ $index === 0 ? 'bx-user' : ($index === 1 ? 'bx-briefcase' : 'bx-crown') }} fs-3"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Package Name -->
                                        <h5 class="mb-2">{{ $package->name }}</h5>
                                        <p class="text-muted small mb-3">{{ $package->description }}</p>

                                        <!-- Price -->
                                        <div class="mb-3">
                                            <h3 class="text-primary mb-0 package-price" data-monthly="{{ $package->base_price }}" data-annual="{{ $package->base_price }}">
                                                Rp {{ number_format($package->base_price, 0, ',', '.') }}
                                            </h3>
                                            <small class="text-muted">/tahun</small>
                                        </div>

                                        <!-- Features -->
                                        <div class="text-start mb-3">
                                            @if($package->features)
                                                @foreach($package->features as $feature => $value)
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="bx bx-check text-success me-2"></i>
                                                        <small class="text-muted">
                                                            @if(is_bool($value))
                                                                {{ ucfirst(str_replace('_', ' ', $feature)) }}
                                                            @else
                                                                {{ $value }} {{ str_replace('_', ' ', $feature) }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>

                                        <!-- Select Button -->
                                        <div class="select-indicator d-none">
                                            <i class="bx bx-check-circle text-success fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="bx bx-info-circle me-2"></i>
                                    Tidak ada paket layanan yang tersedia saat ini.
                                </div>
                            </div>
                            @endforelse
                        </div>

                        <input type="hidden" name="package_id" id="package_id" value="" required>
                        @error('package_id')
                            <div class="text-danger small mt-2">
                                <i class="bx bx-error-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Domain Input -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bx bx-globe me-2"></i>Informasi Domain
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="domain_name" class="form-label">
                                Nama Domain <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text">https://</span>
                                        <input type="text" class="form-control @error('domain_name') is-invalid @enderror" 
                                               id="domain_name" name="domain_name" 
                                               value="{{ old('domain_name') }}"
                                               placeholder="websiteanda" required
                                               oninput="checkDomainAvailability()">
                                        <span class="input-group-text" id="domainStatus">
                                            <i class="bx bx-time"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-select @error('domain_extension') is-invalid @enderror" 
                                            id="domain_extension" 
                                            name="domain_extension"
                                            onchange="updateDomainPricing()">
                                        <option value="">Pilih Extension</option>
                                        @foreach($groupedDomains as $extension => $domains)
                                            <optgroup label=".{{ $extension }}">
                                                @foreach($domains as $domain)
                                                    <option value="{{ $domain->id }}" 
                                                            data-price="{{ $domain->price }}"
                                                            data-extension="{{ $domain->extension }}"
                                                            data-duration="{{ $domain->duration_years }}"
                                                            {{ old('domain_extension') == $domain->id ? 'selected' : '' }}>
                                                        .{{ $domain->extension }} ({{ $domain->duration_years }} tahun) - {{ $domain->formatted_price }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @error('domain_name')
                                <div class="text-danger small mt-1">
                                    <i class="bx bx-error-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                            @error('domain_extension')
                                <div class="text-danger small mt-1">
                                    <i class="bx bx-error-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                            <div class="form-text" id="domainMessage">
                                <i class="bx bx-info-circle me-1"></i>
                                Masukkan nama domain dan pilih extension yang Anda inginkan. Domain yang termasuk dalam paket promo akan ditandai GRATIS.
                                <br><small class="text-muted">*Pengecekan domain termasuk duplikat di sistem kami dan ketersediaan global via DNS lookup.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Tambahkan catatan atau permintaan khusus...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 80px;">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bx bx-receipt me-2"></i>Ringkasan Order
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Selected Package -->
                        <div class="mb-3" id="selectedPackageInfo">
                            <p class="text-muted text-center py-4">
                                <i class="bx bx-package fs-1 d-block mb-2"></i>
                                Pilih paket layanan terlebih dahulu
                            </p>
                        </div>

                        <div id="orderDetails" class="d-none">
                            <!-- Package Name -->
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Paket:</span>
                                <span class="fw-semibold" id="summaryPackage">-</span>
                            </div>

                            <!-- Billing Cycle -->
                            <div class="mb-3">
                                <label class="form-label">Periode Langganan:</label>
                                <div class="d-flex gap-2">
                                    <div class="form-check flex-fill">
                                        <input class="form-check-input" type="radio" name="billing_cycle" 
                                               id="billingAnnual" value="annually" checked>
                                        <label class="form-check-label" for="billingAnnual">
                                            Tahunan
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Price Breakdown -->
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Harga Paket/Tahun:</span>
                                <span id="summaryBasePrice">Rp 0</span>
                            </div>
                            
                            <!-- Domain Pricing -->
                            <div id="domainPricingSection" class="d-none">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted" id="domainLabel">Domain:</span>
                                    <span id="summaryDomainPrice">Rp 0</span>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Periode:</span>
                                <span>12 Bulan</span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between mb-4">
                                <span class="fw-semibold">Total:</span>
                                <span class="fw-bold text-primary fs-5" id="summaryTotal">Rp 0</span>
                            </div>

                            <input type="hidden" name="billing_cycle" value="annually">
                            <input type="hidden" name="domain_full" id="domain_full" value="">
                            <input type="hidden" name="domain_extension_id" id="domain_extension_id" value="">

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100 mb-3" id="submitBtn" disabled>
                                <i class="bx bx-cart me-1"></i>Buat Order
                            </button>

                            <div class="text-center">
                                <small class="text-muted">
                                    <i class="bx bx-lock me-1"></i>
                                    Pembayaran aman & terenkripsi
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.package-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.package-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.package-card.selected {
    border-color: #696cff !important;
    border-width: 2px;
    background-color: rgba(105, 108, 255, 0.05);
}

.package-card.selected .select-indicator {
    display: block !important;
}
</style>

<script>
let selectedPackage = null;
let selectedDomainExtension = null;

function selectPackage(packageId) {
    // Remove selected class from all cards
    document.querySelectorAll('.package-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selected class to clicked card
    const selectedCard = document.querySelector(`[data-package-id="${packageId}"]`);
    selectedCard.classList.add('selected');
    
    // Get package data from data attributes
    const packageName = selectedCard.dataset.packageName;
    const basePrice = parseFloat(selectedCard.dataset.basePrice);
    const hasDomainPromo = selectedCard.dataset.domainPromo === 'true';
    const domainExtensionId = selectedCard.dataset.domainExtensionId || '';
    const domainDuration = selectedCard.dataset.domainDuration || '';
    const domainDiscount = parseFloat(selectedCard.dataset.domainDiscount) || 0;
    const isDomainFree = selectedCard.dataset.domainFree === 'true';
    
    selectedPackage = {
        id: packageId,
        name: packageName,
        basePrice: basePrice,
        hasDomainPromo: hasDomainPromo,
        domainExtensionId: domainExtensionId,
        domainDuration: domainDuration,
        domainDiscount: domainDiscount,
        isDomainFree: isDomainFree
    };
    
    // Update hidden input
    document.getElementById('package_id').value = packageId;
    
    // Show order details
    document.getElementById('selectedPackageInfo').classList.add('d-none');
    document.getElementById('orderDetails').classList.remove('d-none');
    
    // Update summary
    document.getElementById('summaryPackage').textContent = packageName;
    
    // Auto-select domain if package has promo
    if (hasDomainPromo && domainExtensionId) {
        document.getElementById('domain_extension').value = domainExtensionId;
        updateDomainPricing();
    }
    
    // Update price
    updatePrice();
    
    // Enable submit button
    document.getElementById('submitBtn').disabled = false;
}

function updateDomainPricing() {
    const domainSelect = document.getElementById('domain_extension');
    const selectedOption = domainSelect.options[domainSelect.selectedIndex];
    
    if (domainSelect.value) {
        selectedDomainExtension = {
            id: domainSelect.value,
            extension: selectedOption.dataset.extension,
            duration: selectedOption.dataset.duration,
            price: parseFloat(selectedOption.dataset.price)
        };
        
        // Update hidden inputs
        document.getElementById('domain_extension_id').value = domainSelect.value;
        updateFullDomain();
    } else {
        selectedDomainExtension = null;
        document.getElementById('domain_extension_id').value = '';
    }
    
    updatePrice();
}

function updateFullDomain() {
    const domainName = document.getElementById('domain_name').value.trim();
    const domainExtension = document.getElementById('domain_extension');
    const selectedOption = domainExtension.options[domainExtension.selectedIndex];
    
    if (domainName && domainExtension.value && selectedOption) {
        const extension = selectedOption.dataset.extension;
        const fullDomain = domainName + '.' + extension;
        document.getElementById('domain_full').value = fullDomain;
    } else {
        document.getElementById('domain_full').value = '';
    }
}

function updatePrice() {
    if (!selectedPackage) return;
    
    const basePrice = selectedPackage.basePrice;
    let domainPrice = 0;
    let domainLabel = 'Domain:';
    
    // Calculate domain price
    if (selectedDomainExtension) {
        if (selectedPackage.hasDomainPromo && selectedPackage.domainExtensionId == selectedDomainExtension.id) {
            // Domain is included in package promo
            if (selectedPackage.isDomainFree) {
                domainPrice = 0;
                domainLabel = `Domain .${selectedDomainExtension.extension} (${selectedDomainExtension.duration} tahun):`;
            } else {
                // Apply package discount
                domainPrice = selectedDomainExtension.price * (1 - selectedPackage.domainDiscount / 100);
                domainLabel = `Domain .${selectedDomainExtension.extension} (${selectedDomainExtension.duration} tahun):`;
            }
        } else {
            // Domain not included in package, charge full price
            domainPrice = selectedDomainExtension.price;
            domainLabel = `Domain .${selectedDomainExtension.extension} (${selectedDomainExtension.duration} tahun):`;
        }
    }
    
    // Calculate total
    const totalPrice = basePrice + domainPrice;
    
    // Update price display
    document.getElementById('summaryBasePrice').textContent = 'Rp ' + formatNumber(basePrice);
    
    // Show/hide domain pricing section
    const domainPricingSection = document.getElementById('domainPricingSection');
    if (selectedDomainExtension) {
        domainPricingSection.classList.remove('d-none');
        document.getElementById('domainLabel').textContent = domainLabel;
        document.getElementById('summaryDomainPrice').textContent = domainPrice === 0 ? 'FREE' : 'Rp ' + formatNumber(domainPrice);
    } else {
        domainPricingSection.classList.add('d-none');
    }
    
    document.getElementById('summaryTotal').textContent = 'Rp ' + formatNumber(totalPrice);
}

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(Math.round(num));
}

// Domain availability checking
let domainCheckTimeout;
let domainCheckStartTime;
function checkDomainAvailability() {
    const domainName = document.getElementById('domain_name').value.trim();
    const domainExtension = document.getElementById('domain_extension');
    const selectedOption = domainExtension.options[domainExtension.selectedIndex];
    const statusElement = document.getElementById('domainStatus');
    const messageElement = document.getElementById('domainMessage');
    
    // Update full domain
    updateFullDomain();
    
    // Clear timeout
    clearTimeout(domainCheckTimeout);
    
    if (domainName.length < 3 || !domainExtension.value) {
        statusElement.innerHTML = '<i class="bx bx-time"></i>';
        messageElement.innerHTML = '<i class="bx bx-info-circle me-1"></i>Masukkan nama domain dan pilih extension yang Anda inginkan. Domain yang termasuk dalam paket promo akan ditandai GRATIS.';
        messageElement.className = 'form-text text-muted';
        return;
    }
    
    const extension = selectedOption ? selectedOption.dataset.extension : '';
    const fullDomain = domainName + '.' + extension;
    
    // Reset status to checking
    statusElement.innerHTML = '<i class="bx bx-loader-alt bx-spin text-info"></i>';
    messageElement.innerHTML = '<i class="bx bx-info-circle me-1"></i>Mengecek ketersediaan domain (global)...';
    messageElement.className = 'form-text text-info';
    
    // Debounce check with longer delay for DNS operations
    domainCheckTimeout = setTimeout(() => {
        domainCheckStartTime = Date.now();
        
        // Show loading spinner with timeout warning
        const loadingTimeout = setTimeout(() => {
            if (Date.now() - domainCheckStartTime > 2000) {
                messageElement.innerHTML = '<i class="bx bx-info-circle me-1"></i>Mengecek ketersediaan global... (memerlukan beberapa detik)';
            }
        }, 2000);
        
        fetch(`/api/check-domain?domain=${encodeURIComponent(fullDomain)}`)
            .then(response => response.json())
            .then(data => {
                clearTimeout(loadingTimeout);
                
                if (data.available === 'unknown') {
                    statusElement.innerHTML = '<i class="bx bx-help-circle text-warning"></i>';
                    messageElement.innerHTML = '<i class="bx bx-help-circle me-1 text-warning"></i>' + data.message;
                    messageElement.className = 'form-text text-warning';
                } else if (data.available) {
                    statusElement.innerHTML = '<i class="bx bx-check text-success"></i>';
                    messageElement.innerHTML = '<i class="bx bx-check-circle me-1 text-success"></i>' + data.message;
                    messageElement.className = 'form-text text-success';
                } else {
                    statusElement.innerHTML = '<i class="bx bx-x text-danger"></i>';
                    let errorMessage = data.message;
                    
                    // Add specific guidance based on check type
                    if (data.local_check === 'taken') {
                        errorMessage += ' (duplikat di sistem kami)';
                    } else if (data.global_check === 'taken') {
                        errorMessage += ' (sudah digunakan secara global)';
                    }
                    
                    messageElement.innerHTML = '<i class="bx bx-error-circle me-1 text-danger"></i>' + errorMessage + '. Silakan pilih domain lain.';
                    messageElement.className = 'form-text text-danger';
                }
            })
            .catch(error => {
                clearTimeout(loadingTimeout);
                statusElement.innerHTML = '<i class="bx bx-error text-warning"></i>';
                messageElement.innerHTML = '<i class="bx bx-error-circle me-1 text-warning"></i>Gagal mengecek domain. Silakan coba lagi.';
                messageElement.className = 'form-text text-warning';
            });
    }, 1000); // Increased debounce to 1 second for DNS operations
}

// Auto-select first package if only one exists
document.addEventListener('DOMContentLoaded', function() {
    const packages = document.querySelectorAll('.package-card');
    if (packages.length === 1) {
        selectPackage(packages[0].dataset.packageId);
    }
});

// Listen for domain name changes
document.getElementById('domain_name').addEventListener('input', function() {
    updateFullDomain();
});
</script>
@endsection
