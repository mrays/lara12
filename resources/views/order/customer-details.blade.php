@extends('layouts.guest')

@section('title', 'Customer Details - Order')

@section('content')
<div class="container-xxl py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Progress Steps -->
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex flex-column align-items-center">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bx bx-check"></i>
                        </div>
                        <small class="mt-2 text-success">Domain</small>
                    </div>
                    <div class="flex-grow-1 mx-2">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-success" style="width: 66%"></div>
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-center">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bx bx-check"></i>
                        </div>
                        <small class="mt-2 text-success">Package</small>
                    </div>
                    <div class="flex-grow-1 mx-2">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-success" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bx bx-user"></i>
                        </div>
                        <small class="mt-2 text-primary">Details</small>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card bg-light mb-4">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="bx bx-shopping-bag me-2"></i>Order Summary
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <small class="text-muted">Domain</small>
                                <div class="fw-bold">{{ $orderData['full_domain'] }}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Hosting Package</small>
                                <div class="fw-bold">{{ $orderData['package_name'] }}</div>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="mb-2">
                                <small class="text-muted">Domain Registration</small>
                                <div class="fw-bold">Rp {{ number_format($orderData['extension_price'], 0, ',', '.') }}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Hosting Package</small>
                                <div class="fw-bold">Rp {{ number_format($orderData['package_price'], 0, ',', '.') }}</div>
                            </div>
                            <hr class="my-2">
                            <div>
                                <small class="text-muted">Total (First Year)</small>
                                <div class="fw-bold text-success fs-5">Rp {{ number_format($orderData['total_price'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Details Form -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bx bx-user-plus me-2"></i>Create Your Account
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('order.register-customer') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        
                        <!-- Account Information -->
                        <div class="mb-4">
                            <h6 class="mb-3">
                                <i class="bx bx-user me-2"></i>Account Information
                            </h6>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" 
                                           name="name" 
                                           class="form-control" 
                                           placeholder="John Doe"
                                           value="{{ old('name') }}"
                                           required>
                                    <div class="invalid-feedback">
                                        Please enter your full name.
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Email Address *</label>
                                    <input type="email" 
                                           name="email" 
                                           class="form-control" 
                                           placeholder="john@example.com"
                                           value="{{ old('email') }}"
                                           required>
                                    <div class="invalid-feedback">
                                        Please enter a valid email address.
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number *</label>
                                    <input type="tel" 
                                           name="phone" 
                                           class="form-control" 
                                           placeholder="+62 812-3456-7890"
                                           value="{{ old('phone') }}"
                                           required>
                                    <div class="invalid-feedback">
                                        Please enter your phone number.
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" 
                                              class="form-control" 
                                              rows="2"
                                              placeholder="123 Main St, City, Country">{{ old('address') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <h6 class="mb-3">
                                <i class="bx bx-lock me-2"></i>Account Security
                            </h6>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Password *</label>
                                    <div class="input-group">
                                        <input type="password" 
                                               name="password" 
                                               class="form-control" 
                                               placeholder="Enter password"
                                               id="password"
                                               required
                                               minlength="6">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="bx bx-show"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">
                                        Minimum 6 characters. Use a mix of letters and numbers for better security.
                                    </div>
                                    <div class="invalid-feedback">
                                        Password must be at least 6 characters long.
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Confirm Password *</label>
                                    <div class="input-group">
                                        <input type="password" 
                                               name="password_confirmation" 
                                               class="form-control" 
                                               placeholder="Confirm password"
                                               id="passwordConfirmation"
                                               required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                            <i class="bx bx-show"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback">
                                        Please confirm your password.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="termsCheck" 
                                       required>
                                <label class="form-check-label" for="termsCheck">
                                    I agree to the <a href="#" class="text-primary">Terms of Service</a> and <a href="#" class="text-primary">Privacy Policy</a> *
                                </label>
                                <div class="invalid-feedback">
                                    You must agree to the terms and conditions.
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bx bx-user-plus me-2"></i>Create Account & Continue to Checkout
                            </button>
                        </div>

                        <!-- Security Note -->
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="bx bx-shield me-1"></i>
                                Your information is secure and encrypted. We'll create your account and setup your services immediately.
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bx-show');
        icon.classList.add('bx-hide');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('bx-hide');
        icon.classList.add('bx-show');
    }
});

document.getElementById('togglePasswordConfirmation').addEventListener('click', function() {
    const passwordInput = document.getElementById('passwordConfirmation');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bx-show');
        icon.classList.add('bx-hide');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('bx-hide');
        icon.classList.add('bx-show');
    }
});

// Form validation
(function () {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            // Check password confirmation
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('passwordConfirmation').value;
            
            if (password !== passwordConfirmation) {
                event.preventDefault();
                event.stopPropagation();
                
                // Show error for password confirmation
                const confirmationField = document.getElementById('passwordConfirmation');
                confirmationField.setCustomValidity('Passwords do not match');
                confirmationField.classList.add('is-invalid');
            } else {
                // Clear custom validity
                document.getElementById('passwordConfirmation').setCustomValidity('');
            }
            
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();

// Clear password confirmation error when typing
document.getElementById('passwordConfirmation').addEventListener('input', function() {
    this.setCustomValidity('');
    this.classList.remove('is-invalid');
});
</script>
@endsection
