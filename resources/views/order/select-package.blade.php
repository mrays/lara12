@extends('layouts.app')

@section('title', 'Select Package - Order')

@section('content')
<div class="container-xxl py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Welcome Message -->
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-check-circle fs-4 me-3"></i>
                    <div>
                        <h6 class="alert-heading mb-1">Welcome {{ auth()->user()->name }}!</h6>
                        <span>Your account has been created successfully. Now choose your hosting package and complete your order.</span>
                    </div>
                </div>
            </div>

            <!-- Selected Domain Info -->
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="alert-heading mb-1">
                            <i class="bx bx-globe me-2"></i>Selected Domain
                        </h6>
                        <span class="fs-5 fw-bold">{{ session('order.full_domain') }}</span>
                    </div>
                    <div class="text-end">
                        <small class="d-block text-muted">Domain Registration</small>
                        <span class="fs-5 fw-bold text-success">{{ $domainExtension->formatted_price }}</span>
                    </div>
                </div>
            </div>

            <!-- Package Selection -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bx bx-package me-2"></i>Choose Your Hosting Package
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('order.submit-order') }}" method="POST">
                        @csrf
                        
                        <div class="row g-4">
                            @foreach($packages as $package)
                                <div class="col-lg-4">
                                    <div class="card h-100 package-card {{ old('package_id') == $package->id ? 'border-primary' : '' }}" 
                                         style="cursor: pointer;"
                                         onclick="selectPackage({{ $package->id }})">
                                        
                                        @if($package->name == 'Premium' || $package->name == 'Business')
                                            <div class="card-header bg-warning text-warning-emphasis text-center py-2">
                                                <small class="fw-bold">MOST POPULAR</small>
                                            </div>
                                        @endif
                                        
                                        <div class="card-body text-center">
                                            <h5 class="card-title text-primary mb-3">{{ $package->name }}</h5>
                                            
                                            <div class="mb-3">
                                                <span class="fs-1 fw-bold">{{ $package->formatted_price }}</span>
                                                <span class="text-muted">/year</span>
                                            </div>
                                            
                                            @if($package->description)
                                                <p class="card-text text-muted mb-4">{{ $package->description }}</p>
                                            @endif
                                            
                                            <!-- Package Features (you can customize these based on your actual package features) -->
                                            <ul class="list-unstyled mb-4">
                                                @if($package->name == 'Basic')
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>5 GB Storage</li>
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>100 GB Bandwidth</li>
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>10 Email Accounts</li>
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>1 MySQL Database</li>
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>Free SSL Certificate</li>
                                                @elseif($package->name == 'Premium')
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>20 GB Storage</li>
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>500 GB Bandwidth</li>
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>50 Email Accounts</li>
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>10 MySQL Databases</li>
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>Free SSL + Domain</li>
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>Priority Support</li>
                                                @elseif($package->name == 'Business')
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>50 GB Storage</li>
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>Unlimited Bandwidth</li>
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>Unlimited Email</li>
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>Unlimited Databases</li>
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>Free SSL + Domain</li>
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>24/7 Phone Support</li>
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>Daily Backups</li>
                                                @else
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>Custom Features</li>
                                                    <li class="mb-2"><i class="bx bx-check text-success me-2"></i>Contact Support</li>
                                                @endif
                                            </ul>
                                            
                                            <div class="d-grid">
                                                <button type="button" 
                                                        class="btn {{ old('package_id') == $package->id ? 'btn-primary' : 'btn-outline-primary' }}"
                                                        onclick="selectPackage({{ $package->id }})">
                                                    {{ old('package_id') == $package->id ? 'Selected' : 'Select Package' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Hidden input for selected package -->
                        <input type="hidden" name="package_id" id="selectedPackageId" value="{{ old('package_id') }}" required>

                        <!-- Price Summary -->
                        <div class="mt-4 pt-4 border-top" id="priceSummary" style="display: {{ old('package_id') ? 'block' : 'none' }};">
                            <div class="row justify-content-end">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title mb-3">Order Summary</h6>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Domain Registration ({{ session('order.full_domain') }})</span>
                                                <span>{{ $domainExtension->formatted_price }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Hosting Package</span>
                                                <span id="packagePriceText">-</span>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between fw-bold">
                                                <span>Total (First Year)</span>
                                                <span class="text-success" id="totalPriceText">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Complete Order Button -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success btn-lg" id="continueBtn" disabled>
                                <i class="bx bx-check-double me-2"></i>Complete Order & Go to Dashboard
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const packagePrices = {};
@foreach($packages as $package)
    packagePrices[{{ $package->id }}] = '{{ $package->formatted_price }}';
@endforeach

function selectPackage(packageId) {
    // Update hidden input
    document.getElementById('selectedPackageId').value = packageId;
    
    // Update card styles
    document.querySelectorAll('.package-card').forEach(card => {
        card.classList.remove('border-primary');
        const btn = card.querySelector('button');
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
        btn.textContent = 'Select Package';
    });
    
    const selectedCard = document.querySelector(`[onclick="selectPackage(${packageId})"]`);
    selectedCard.classList.add('border-primary');
    const selectedBtn = selectedCard.querySelector('button');
    selectedBtn.classList.remove('btn-outline-primary');
    selectedBtn.classList.add('btn-primary');
    selectedBtn.textContent = 'Selected';
    
    // Update price summary
    updatePriceSummary(packageId);
    
    // Enable continue button
    document.getElementById('continueBtn').disabled = false;
}

function updatePriceSummary(packageId) {
    const packagePrice = packagePrices[packageId];
    const domainPrice = '{{ $domainExtension->formatted_price }}';
    
    // Parse prices (remove currency symbols and convert to number)
    const packageNum = parseFloat(packagePrice.replace(/[^0-9.]/g, ''));
    const domainNum = parseFloat(domainPrice.replace(/[^0-9.]/g, ''));
    const total = packageNum + domainNum;
    
    document.getElementById('packagePriceText').textContent = packagePrice;
    document.getElementById('totalPriceText').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('priceSummary').style.display = 'block';
}

// Initialize if package is pre-selected
@if(old('package_id'))
    updatePriceSummary({{ old('package_id') }});
    document.getElementById('continueBtn').disabled = false;
@endif
</script>
@endpush
