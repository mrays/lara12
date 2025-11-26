@extends('layouts.app')

@section('title', 'Checkout - Complete Your Order')

@section('content')
<div class="container-xxl py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Welcome Message -->
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-check-circle fs-4 me-3"></i>
                    <div>
                        <h6 class="alert-heading mb-1">Account Created Successfully!</h6>
                        <span>Welcome {{ $user->name }}! You are now logged in and ready to complete your order.</span>
                    </div>
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bx bx-shopping-bag me-2"></i>Order Summary
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Order Details -->
                        <div class="col-md-8">
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Domain Registration</h6>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bx bx-globe text-primary me-2 fs-4"></i>
                                    <div>
                                        <div class="fw-bold fs-5">{{ $orderData['full_domain'] }}</div>
                                        <small class="text-muted">1 Year Registration</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Hosting Package</h6>
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-package text-success me-2 fs-4"></i>
                                    <div>
                                        <div class="fw-bold fs-5">{{ $orderData['package_name'] }}</div>
                                        <small class="text-muted">1 Year Hosting</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Account Information</h6>
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-user text-info me-2 fs-4"></i>
                                    <div>
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Price Summary -->
                        <div class="col-md-4">
                            <div class="bg-light rounded p-3">
                                <h6 class="mb-3">Price Breakdown</h6>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Domain Registration</span>
                                    <span>Rp {{ number_format($orderData['extension_price'], 0, ',', '.') }}</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ $orderData['package_name'] }} Hosting</span>
                                    <span>Rp {{ number_format($orderData['package_price'], 0, ',', '.') }}</span>
                                </div>
                                
                                <hr class="my-2">
                                
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Total (First Year)</span>
                                    <span class="text-success fs-5">Rp {{ number_format($orderData['total_price'], 0, ',', '.') }}</span>
                                </div>
                                
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="bx bx-info-circle me-1"></i>
                                        Renewal price may vary. You'll be notified before renewal.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-credit-card me-2"></i>Payment Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info" role="alert">
                        <h6 class="alert-heading">
                            <i class="bx bx-info-circle me-2"></i>How Payment Works
                        </h6>
                        <ol class="mb-0">
                            <li>Click "Complete Order" to create your invoice</li>
                            <li>You'll be redirected to your dashboard</li>
                            <li>Complete payment through our secure payment gateway</li>
                            <li>Your services will be activated within 24 hours</li>
                        </ol>
                    </div>

                    <!-- Payment Methods -->
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3">
                                <i class="bx bx-credit-card text-primary fs-2 mb-2"></i>
                                <div class="small">Credit Card</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3">
                                <i class="bx bx-mobile text-success fs-2 mb-2"></i>
                                <div class="small">Bank Transfer</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3">
                                <i class="bx bx-wallet text-warning fs-2 mb-2"></i>
                                <div class="small">E-Wallet</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3">
                                <i class="bx bx-store text-info fs-2 mb-2"></i>
                                <div class="small">Convenience Store</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terms and Complete Order -->
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('order.submit-order') }}" method="POST">
                        @csrf
                        
                        <!-- Terms Agreement -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="finalTermsCheck" required>
                                <label class="form-check-label" for="finalTermsCheck">
                                    I confirm that all information is correct and I agree to the 
                                    <a href="#" class="text-primary">Terms of Service</a> and 
                                    <a href="#" class="text-primary">Privacy Policy</a>
                                </label>
                            </div>
                        </div>

                        <!-- Complete Order Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bx bx-check-double me-2"></i>
                                Complete Order - Rp {{ number_format($orderData['total_price'], 0, ',', '.') }}
                            </button>
                        </div>

                        <!-- Security Note -->
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="bx bx-shield me-1"></i>
                                Secure checkout protected by SSL encryption
                            </small>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Need Help -->
            <div class="text-center mt-4">
                <small class="text-muted">
                    Need help? Contact our support team at 
                    <a href="mailto:support@example.com" class="text-primary">support@example.com</a> or 
                    <a href="tel:+62123456789" class="text-primary">+62 123-456-789</a>
                </small>
            </div>
        </div>
    </div>
</div>
@endsection
