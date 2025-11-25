@extends('layouts.sneat-dashboard')

@section('title', 'Payment - ' . $invoice->number)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <!-- Invoice Summary Card -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bx bx-receipt me-2"></i>Invoice Payment
                    </h5>
                    <span class="badge bg-{{ $invoice->status == 'Paid' || $invoice->status == 'Lunas' ? 'success' : ($invoice->status == 'Overdue' ? 'danger' : 'warning') }}">{{ $invoice->status }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Invoice Details</h6>
                            <p class="mb-1"><strong>Invoice Number:</strong> {{ $invoice->number }}</p>
                            <p class="mb-1"><strong>Title:</strong> {{ $invoice->title }}</p>
                            <p class="mb-1"><strong>Issue Date:</strong> {{ $invoice->issue_date ? $invoice->issue_date->format('d M Y') : 'N/A' }}</p>
                            <p class="mb-1"><strong>Due Date:</strong> {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'N/A' }}</p>
                            @if($invoice->status == 'Overdue')
                                <p class="mb-1 text-danger">
                                    <strong>Status:</strong> Overdue
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Client Information</h6>
                            <p class="mb-1"><strong>Name:</strong> {{ $currentUser->name ?? $invoice->client->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $currentUser->email ?? $invoice->client->email ?? 'N/A' }}</p>
                            @if($currentUser->business_name ?? $invoice->client->company)
                                <p class="mb-1"><strong>Company:</strong> {{ $currentUser->business_name ?? $invoice->client->company }}</p>
                            @endif
                            @if($currentUser->whatsapp ?? $currentUser->phone ?? $invoice->client->phone)
                                <p class="mb-1"><strong>Phone:</strong> {{ $currentUser->whatsapp ?? $currentUser->phone ?? $invoice->client->phone }}</p>
                            @endif
                            @if($currentUser->address)
                                <p class="mb-1"><strong>Address:</strong> {{ $currentUser->address }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Amount Summary -->
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Subtotal:</strong></td>
                                    <td class="text-end">Rp {{ number_format($invoice->subtotal ?? $invoice->total_amount, 0, ',', '.') }}</td>
                                </tr>
                                @if($invoice->tax_amount > 0)
                                <tr>
                                    <td>Tax ({{ $invoice->tax_rate }}%):</td>
                                    <td class="text-end">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                @if($invoice->discount_amount > 0)
                                <tr>
                                    <td>Discount:</td>
                                    <td class="text-end text-success">-Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                <tr class="border-top">
                                    <td><strong>Total Amount:</strong></td>
                                    <td class="text-end"><strong class="text-primary fs-4">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @if(in_array($invoice->status, ['Unpaid', 'gagal', 'Overdue']))
                <!-- Payment Methods Card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-credit-card me-2"></i>Choose Payment Method
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($invoice->merchant_order_id)
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                You have a pending payment for this invoice. 
                                <a href="#" class="btn btn-sm btn-primary ms-2" target="_blank">
                                    Continue Payment
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-1" onclick="checkPaymentStatus()">
                                    Check Status
                                </button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <h6><i class="bx bx-error me-2"></i>Please fix the following errors:</h6>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                <i class="bx bx-error me-2"></i>{{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('payment.process', $invoice) }}" method="POST" id="paymentForm">
                            @csrf
                            
                            <!-- Customer Information (REQUIRED like PHP native) -->
                            <div class="mb-4">
                                <h6 class="text-muted mb-3">
                                    <i class="bx bx-user me-2"></i>Customer Information <span class="text-danger">*</span>
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_name" class="form-label">
                                            Full Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                               id="customer_name" name="customer_name" required
                                               value="{{ old('customer_name', $currentUser->name ?? $invoice->client->name) }}" 
                                               placeholder="Enter your full name">
                                        @error('customer_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_email" class="form-label">
                                            Email Address <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                               id="customer_email" name="customer_email" required
                                               value="{{ old('customer_email', $currentUser->email ?? $invoice->client->email) }}" 
                                               placeholder="your@email.com">
                                        @error('customer_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_phone" class="form-label">
                                            Phone Number <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" 
                                               id="customer_phone" name="customer_phone" required
                                               value="{{ old('customer_phone', $currentUser->whatsapp ?? $currentUser->phone ?? $invoice->client->phone) }}" 
                                               placeholder="08xxxxxxxxxx" pattern="[0-9+]{10,15}">
                                        @error('customer_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="alert alert-info mt-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-info-circle me-2"></i>
                                        <div class="flex-grow-1">
                                            <small>
                                                <strong>Data diambil dari profile Anda.</strong><br>
                                                Jika data tidak sesuai, Anda dapat mengubahnya di form atau 
                                                <a href="{{ route('profile.edit') }}" target="_blank" class="text-decoration-underline">
                                                    update profile Anda
                                                </a>.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Methods -->
                            <div class="mb-4">
                                <h6 class="text-muted mb-3">
                                    <i class="bx bx-credit-card me-2"></i>Choose Payment Method <span class="text-danger">*</span>
                                </h6>
                                <div class="row">
                                    @foreach($paymentMethods as $code => $method)
                                        <div class="col-md-4 col-sm-6 mb-3">
                                            <div class="card payment-method-card" style="cursor: pointer;" onclick="selectPaymentMethod('{{ $code }}')">
                                                <div class="card-body text-center">
                                                    <div class="payment-method-icon mb-2">
                                                        @switch($code)
                                                            @case('SP')
                                                                <i class="bx bx-mobile" style="font-size: 2rem; color: #ee4d2d;"></i>
                                                                @break
                                                            @case('DA')
                                                                <i class="bx bx-wallet" style="font-size: 2rem; color: #009cff;"></i>
                                                                @break
                                                            @case('M2')
                                                                <i class="bx bx-credit-card" style="font-size: 2rem; color: #004CAD;"></i>
                                                                @break
                                                            @case('B1')
                                                                <i class="bx bx-credit-card" style="font-size: 2rem; color: #0066cc;"></i>
                                                                @break
                                                            @case('BR')
                                                                <i class="bx bx-credit-card" style="font-size: 2rem; color: #003d82;"></i>
                                                                @break
                                                            @case('I1')
                                                                <i class="bx bx-credit-card" style="font-size: 2rem; color: #0066CC;"></i>
                                                                @break
                                                            @case('B1')
                                                                <i class="bx bx-credit-card" style="font-size: 2rem; color: #FF8B00;"></i>
                                                                @break
                                                            @default
                                                                <i class="bx bx-credit-card" style="font-size: 2rem; color: #6c757d;"></i>
                                                        @endswitch
                                                    </div>
                                                    <h6 class="mb-0">{{ $method['name'] }}</h6>
                                                    <small class="text-muted">{{ $method['type'] == 'ewallet' ? 'E-Wallet' : 'Virtual Account' }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <input type="hidden" name="payment_method" id="selectedPaymentMethod">
                            
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg" id="payButton" disabled>
                                    <i class="bx bx-credit-card me-2"></i>Pay Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                </button>
                                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-lg ms-2">
                                    <i class="bx bx-arrow-back me-2"></i>Back
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <!-- Invoice Already Paid -->
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bx bx-check-circle text-success" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">Invoice Already Paid</h4>
                        <p class="text-muted">This invoice has been paid and cannot be processed again.</p>
                        @if($invoice->paid_date)
                            <p><strong>Paid on:</strong> {{ $invoice->paid_date->format('d M Y H:i') }}</p>
                        @endif
                        @if($invoice->payment_method)
                            <p><strong>Payment Method:</strong> {{ $invoice->payment_method }}</p>
                        @endif
                        @if($invoice->payment_reference)
                            <p><strong>Reference:</strong> {{ $invoice->payment_reference }}</p>
                        @endif
                        <a href="{{ url()->previous() }}" class="btn btn-primary mt-3">
                            <i class="bx bx-arrow-back me-2"></i>Back to Invoice
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.payment-method-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.payment-method-card:hover {
    border-color: #696cff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.payment-method-card.selected {
    border-color: #696cff;
    background-color: #f8f9ff;
}

.payment-method-icon {
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<script>
// Form validation like PHP native
function validateForm() {
    const customerName = document.getElementById('customer_name').value.trim();
    const customerEmail = document.getElementById('customer_email').value.trim();
    const customerPhone = document.getElementById('customer_phone').value.trim();
    const paymentMethod = document.getElementById('selectedPaymentMethod').value;
    
    if (!customerName) {
        alert('Please enter your full name');
        document.getElementById('customer_name').focus();
        return false;
    }
    
    if (!customerEmail) {
        alert('Please enter your email address');
        document.getElementById('customer_email').focus();
        return false;
    }
    
    if (!customerEmail.includes('@') || !customerEmail.includes('.')) {
        alert('Please enter a valid email address');
        document.getElementById('customer_email').focus();
        return false;
    }
    
    if (!customerPhone || customerPhone.length < 10) {
        alert('Please enter a valid phone number (minimum 10 digits)');
        document.getElementById('customer_phone').focus();
        return false;
    }
    
    if (!paymentMethod) {
        alert('Please select a payment method');
        return false;
    }
    
    return true;
}

function selectPaymentMethod(method) {
    // Remove previous selection
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selection to clicked card
    event.currentTarget.classList.add('selected');
    
    // Set hidden input value
    document.getElementById('selectedPaymentMethod').value = method;
    
    // Enable pay button if form is valid
    updatePayButton();
}

function updatePayButton() {
    const customerName = document.getElementById('customer_name').value.trim();
    const customerEmail = document.getElementById('customer_email').value.trim();
    const customerPhone = document.getElementById('customer_phone').value.trim();
    const paymentMethod = document.getElementById('selectedPaymentMethod').value;
    
    const isValid = customerName && customerEmail && customerPhone && paymentMethod;
    document.getElementById('payButton').disabled = !isValid;
}

// Phone number formatting
document.getElementById('customer_phone').addEventListener('input', function() {
    let value = this.value.replace(/[^0-9+]/g, '');
    this.value = value;
    updatePayButton();
});

// Real-time validation
document.getElementById('customer_name').addEventListener('input', updatePayButton);
document.getElementById('customer_email').addEventListener('input', updatePayButton);
document.getElementById('customer_phone').addEventListener('input', updatePayButton);

// Form submission with confirmation like PHP native
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!validateForm()) {
        return false;
    }
    
    const customerName = document.getElementById('customer_name').value.trim();
    const amount = '{{ $invoice->getFormattedAmount() }}';
    const paymentMethod = document.getElementById('selectedPaymentMethod').value;
    
    const confirmMessage = `Confirm Payment\n\n` +
        `Name: ${customerName}\n` +
        `Amount: ${amount}\n` +
        `Payment Method: ${paymentMethod}\n\n` +
        `This will process your payment securely.\n` +
        `Continue?`;
    
    if (!confirm(confirmMessage)) {
        return false;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('payButton');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-2"></i>Processing Payment...';
    
    // Submit form
    this.submit();
});

function checkPaymentStatus() {
    fetch('{{ route("payment.status", $invoice) }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.status === 'paid') {
                    alert('Payment confirmed! Reloading page...');
                    location.reload();
                } else {
                    alert('Payment is still pending. Please complete the payment or try again later.');
                }
            } else {
                alert('Failed to check payment status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error checking payment status');
        });
}

// Auto-check payment status every 30 seconds if there's a pending payment
@if($invoice->hasPendingPayment())
setInterval(function() {
    fetch('{{ route("payment.status", $invoice) }}')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.status === 'paid') {
                location.reload();
            }
        })
        .catch(error => console.log('Auto-check error:', error));
}, 30000);
@endif
</script>
@endsection
