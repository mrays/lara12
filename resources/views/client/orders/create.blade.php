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
                            <label for="domain" class="form-label">
                                Nama Domain <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">https://</span>
                                <input type="text" class="form-control @error('domain') is-invalid @enderror" 
                                       id="domain" name="domain" 
                                       value="{{ old('domain') }}"
                                       placeholder="contoh: websiteanda.com" required
                                       oninput="checkDomainAvailability()">
                                <span class="input-group-text" id="domainStatus">
                                    <i class="bx bx-time"></i>
                                </span>
                            </div>
                            @error('domain')
                                <div class="text-danger small mt-1">
                                    <i class="bx bx-error-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                            <div class="form-text" id="domainMessage">
                                <i class="bx bx-info-circle me-1"></i>
                                Masukkan nama domain yang ingin Anda gunakan. Jika belum punya domain, kami bisa bantu daftarkan.
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
                                <span class="text-muted">Harga Paket/Bulan:</span>
                                <span id="summaryBasePrice">Rp 0</span>
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

function selectPackage(packageId) {
    // Remove selected class from all cards
    document.querySelectorAll('.package-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selected class to clicked card
    const selectedCard = document.querySelector(`[data-package-id="${packageId}"]`);
    selectedCard.classList.add('selected');
    
    // Get package data
    const packageName = selectedCard.dataset.packageName;
    const basePrice = parseFloat(selectedCard.dataset.basePrice);
    
    selectedPackage = {
        id: packageId,
        name: packageName,
        basePrice: basePrice
    };
    
    // Update hidden input
    document.getElementById('package_id').value = packageId;
    
    // Show order details
    document.getElementById('selectedPackageInfo').classList.add('d-none');
    document.getElementById('orderDetails').classList.remove('d-none');
    
    // Update summary
    document.getElementById('summaryPackage').textContent = packageName;
    
    // Update price
    updatePrice();
    
    // Enable submit button
    document.getElementById('submitBtn').disabled = false;
}

function updatePrice() {
    if (!selectedPackage) return;
    
    const basePrice = selectedPackage.basePrice;
    
    // Harga = base_price langsung (tanpa dikali 12)
    const totalPrice = basePrice;
    
    document.getElementById('summaryBasePrice').textContent = 'Rp ' + formatNumber(basePrice);
    document.getElementById('summaryTotal').textContent = 'Rp ' + formatNumber(totalPrice);
}

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(Math.round(num));
}

// Domain availability checking
let domainCheckTimeout;
function checkDomainAvailability() {
    const domain = document.getElementById('domain').value.trim();
    const statusElement = document.getElementById('domainStatus');
    const messageElement = document.getElementById('domainMessage');
    
    // Clear timeout
    clearTimeout(domainCheckTimeout);
    
    // Reset status
    statusElement.innerHTML = '<i class="bx bx-time"></i>';
    messageElement.innerHTML = '<i class="bx bx-info-circle me-1"></i>Mengecek ketersediaan domain...';
    messageElement.className = 'form-text text-muted';
    
    if (domain.length < 3) {
        statusElement.innerHTML = '<i class="bx bx-time"></i>';
        messageElement.innerHTML = '<i class="bx bx-info-circle me-1"></i>Masukkan nama domain yang ingin Anda gunakan. Jika belum punya domain, kami bisa bantu daftarkan.';
        messageElement.className = 'form-text text-muted';
        return;
    }
    
    // Debounce check
    domainCheckTimeout = setTimeout(() => {
        fetch(`/api/check-domain?domain=${encodeURIComponent(domain)}`)
            .then(response => response.json())
            .then(data => {
                if (data.available) {
                    statusElement.innerHTML = '<i class="bx bx-check text-success"></i>';
                    messageElement.innerHTML = '<i class="bx bx-check-circle me-1 text-success"></i>Domain tersedia!';
                    messageElement.className = 'form-text text-success';
                } else {
                    statusElement.innerHTML = '<i class="bx bx-x text-danger"></i>';
                    messageElement.innerHTML = '<i class="bx bx-error-circle me-1 text-danger"></i>Domain sudah digunakan. Silakan pilih domain lain.';
                    messageElement.className = 'form-text text-danger';
                }
            })
            .catch(error => {
                statusElement.innerHTML = '<i class="bx bx-error text-warning"></i>';
                messageElement.innerHTML = '<i class="bx bx-error-circle me-1 text-warning"></i>Gagal mengecek domain. Silakan coba lagi.';
                messageElement.className = 'form-text text-warning';
            });
    }, 500);
}

// Auto-select first package if only one exists
document.addEventListener('DOMContentLoaded', function() {
    const packages = document.querySelectorAll('.package-card');
    if (packages.length === 1) {
        selectPackage(packages[0].dataset.packageId);
    }
});
</script>
@endsection
