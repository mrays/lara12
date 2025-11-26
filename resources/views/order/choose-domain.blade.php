@extends('layouts.guest')

@section('title', 'Choose Domain - Order')

@section('content')
<div class="container-xxl py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Progress Steps -->
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex flex-column align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bx bx-globe"></i>
                        </div>
                        <small class="mt-2 text-primary">Domain</small>
                    </div>
                    <div class="flex-grow-1 mx-2">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-primary" style="width: 33%"></div>
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-center">
                        <div class="bg-light text-muted rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bx bx-package"></i>
                        </div>
                        <small class="mt-2 text-muted">Package</small>
                    </div>
                    <div class="flex-grow-1 mx-2">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-light" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-center">
                        <div class="bg-light text-muted rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bx bx-user"></i>
                        </div>
                        <small class="mt-2 text-muted">Details</small>
                    </div>
                </div>
            </div>

            <!-- Domain Selection Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bx bx-globe me-2"></i>Choose Your Domain
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('order.select-domain') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        
                        <!-- Domain Name Input -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-search me-2"></i>Domain Name
                            </label>
                            <div class="input-group input-group-lg">
                                <input type="text" 
                                       name="domain_name" 
                                       class="form-control" 
                                       placeholder="Enter your domain name"
                                       pattern="[a-zA-Z0-9-]+" 
                                       required
                                       value="{{ old('domain_name') }}">
                                <select name="extension_id" class="form-select" style="max-width: 150px;" required>
                                    <option value="">.ext</option>
                                    @foreach($extensions as $extension)
                                        <option value="{{ $extension->id }}" 
                                                data-price="{{ $extension->formatted_price }}"
                                                {{ old('extension_id') == $extension->id ? 'selected' : '' }}>
                                            .{{ $extension->extension }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-text">
                                Enter only letters, numbers, and hyphens. No spaces or special characters.
                            </div>
                        </div>

                        <!-- Domain Extensions Grid -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-list-ul me-2"></i>Available Extensions
                            </label>
                            <div class="row g-3">
                                @foreach($extensions as $extension)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card border-2 border-light h-100 extension-card" 
                                             style="cursor: pointer;"
                                             onclick="selectExtension({{ $extension->id }})">
                                            <div class="card-body text-center py-3">
                                                <div class="fs-4 fw-bold text-primary mb-2">
                                                    .{{ $extension->extension }}
                                                </div>
                                                <div class="text-muted mb-2">
                                                    {{ $extension->duration_display }}
                                                </div>
                                                <div class="fs-5 fw-semibold text-success">
                                                    {{ $extension->formatted_price }}
                                                </div>
                                                @if($extension->description)
                                                    <small class="text-muted d-block mt-2">
                                                        {{ Str::limit($extension->description, 80) }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Selected Domain Preview -->
                        <div class="mb-4" id="domainPreview" style="display: none;">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">
                                    <i class="bx bx-check-circle me-2"></i>Selected Domain
                                </h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fs-5 fw-bold" id="selectedDomainText"></span>
                                    <span class="fs-5 fw-bold text-success" id="selectedPriceText"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bx bx-right-arrow-alt me-2"></i>Continue to Package Selection
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Features Section -->
            <div class="row mt-5">
                <div class="col-md-4 text-center mb-4">
                    <div class="feature-icon bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bx bx-check-shield fs-3 text-primary"></i>
                    </div>
                    <h6>Secure Registration</h6>
                    <p class="text-muted small">All domains are registered securely with privacy protection included.</p>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="feature-icon bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bx bx-time-five fs-3 text-success"></i>
                    </div>
                    <h6>Instant Setup</h6>
                    <p class="text-muted small">Your domain will be ready to use within minutes of registration.</p>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="feature-icon bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bx bx-headphone fs-3 text-info"></i>
                    </div>
                    <h6>24/7 Support</h6>
                    <p class="text-muted small">Get help from our support team anytime you need assistance.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function selectExtension(extensionId) {
    // Update select dropdown
    document.querySelector('select[name="extension_id"]').value = extensionId;
    
    // Update card styles
    document.querySelectorAll('.extension-card').forEach(card => {
        card.classList.remove('border-primary', 'bg-light');
    });
    event.currentTarget.classList.add('border-primary', 'bg-light');
    
    // Update preview
    updateDomainPreview();
}

function updateDomainPreview() {
    const domainName = document.querySelector('input[name="domain_name"]').value;
    const extensionSelect = document.querySelector('select[name="extension_id"]');
    const selectedOption = extensionSelect.options[extensionSelect.selectedIndex];
    
    if (domainName && extensionSelect.value) {
        const extension = selectedOption.text.replace('.', '');
        const price = selectedOption.dataset.price;
        
        document.getElementById('selectedDomainText').textContent = domainName + '.' + extension;
        document.getElementById('selectedPriceText').textContent = price;
        document.getElementById('domainPreview').style.display = 'block';
    } else {
        document.getElementById('domainPreview').style.display = 'none';
    }
}

// Update preview when domain name changes
document.querySelector('input[name="domain_name"]').addEventListener('input', updateDomainPreview);

// Update preview when extension changes
document.querySelector('select[name="extension_id"]').addEventListener('change', function() {
    updateDomainPreview();
    
    // Update card styles
    document.querySelectorAll('.extension-card').forEach(card => {
        card.classList.remove('border-primary', 'bg-light');
    });
    
    // Highlight selected card
    const selectedCard = document.querySelector(`[onclick="selectExtension(${this.value})"]`);
    if (selectedCard) {
        selectedCard.classList.add('border-primary', 'bg-light');
    }
});

// Form validation
(function () {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>
@endsection
