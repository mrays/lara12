@extends('layouts.app')

@section('title', 'Order Successful')

@section('content')
<div class="container-xxl py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-5 text-center">
                    <!-- Success Icon -->
                    <div class="mb-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="bx bx-check-circle text-success" style="font-size: 60px;"></i>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <h2 class="mb-3">Order Completed Successfully!</h2>
                    <p class="text-muted mb-4">
                        Welcome to our hosting service! Your account has been created and your services are being set up.
                    </p>

                    <!-- Order Details -->
                    <div class="bg-light rounded p-4 mb-4 text-start">
                        <h5 class="mb-3">
                            <i class="bx bx-receipt me-2"></i>Order Details
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <small class="text-muted">Invoice Number</small>
                                    <div class="fw-bold">{{ $invoice->invoice_number }}</div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Domain</small>
                                    <div class="fw-bold">{{ $invoice->service->domain ?? '-' }}</div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Package</small>
                                    <div class="fw-bold">{{ $invoice->service->product ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <small class="text-muted">Total Amount</small>
                                    <div class="fw-bold text-success fs-5">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Due Date</small>
                                    <div class="fw-bold">{{ $invoice->due_date->format('M d, Y') }}</div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Status</small>
                                    <span class="badge bg-warning">Unpaid</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="alert alert-info" role="alert">
                        <h6 class="alert-heading">
                            <i class="bx bx-info-circle me-2"></i>What's Next?
                        </h6>
                        <ol class="mb-0 text-start">
                            <li>Complete your payment to activate your services</li>
                            <li>Check your email for account verification link</li>
                            <li>Access your client dashboard to manage services</li>
                            <li>Your domain will be activated within 24 hours after payment</li>
                        </ol>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-center">
                        <a href="{{ route('payment.show', $invoice) }}" class="btn btn-primary btn-lg">
                            <i class="bx bx-credit-card me-2"></i>Pay Invoice
                        </a>
                        <a href="{{ route('client.dashboard') }}" class="btn btn-outline-primary btn-lg">
                            <i class="bx bx-dashboard me-2"></i>Go to Dashboard
                        </a>
                    </div>

                    <!-- Account Info -->
                    <div class="mt-4 pt-4 border-top">
                        <small class="text-muted">
                            <i class="bx bx-envelope me-1"></i>
                            A confirmation email has been sent to {{ auth()->user()->email }}<br>
                            <i class="bx bx-user me-1"></i>
                            Your account username: {{ auth()->user()->email }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Additional Info Cards -->
            <div class="row mt-4">
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bx bx-cog text-primary fs-1 mb-3"></i>
                            <h6 class="card-title">Service Setup</h6>
                            <p class="card-text small text-muted">
                                Your hosting services are being configured and will be ready within 24 hours after payment confirmation.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bx bx-shield text-success fs-1 mb-3"></i>
                            <h6 class="card-title">Security Features</h6>
                            <p class="card-text small text-muted">
                                Free SSL certificate, DDoS protection, and regular backups are included with your hosting package.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bx bx-headphone text-info fs-1 mb-3"></i>
                            <h6 class="card-title">24/7 Support</h6>
                            <p class="card-text small text-muted">
                                Need help? Our support team is available 24/7 via live chat, email, and phone to assist you.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
