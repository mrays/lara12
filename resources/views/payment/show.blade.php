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
                    <span class="{{ $invoice->getPaymentStatusBadgeClass() }}">{{ $invoice->status }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Invoice Details</h6>
                            <p class="mb-1"><strong>Invoice Number:</strong> {{ $invoice->number }}</p>
                            <p class="mb-1"><strong>Title:</strong> {{ $invoice->title }}</p>
                            <p class="mb-1"><strong>Issue Date:</strong> {{ $invoice->issue_date->format('d M Y') }}</p>
                            <p class="mb-1"><strong>Due Date:</strong> {{ $invoice->due_date->format('d M Y') }}</p>
                            @if($invoice->isOverdue())
                                <p class="mb-1 text-danger">
                                    <strong>Overdue:</strong> {{ $invoice->getDaysOverdue() }} days
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Client Information</h6>
                            <p class="mb-1"><strong>Name:</strong> {{ $invoice->client->name }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $invoice->client->email }}</p>
                            @if($invoice->client->company)
                                <p class="mb-1"><strong>Company:</strong> {{ $invoice->client->company }}</p>
                            @endif
                            @if($invoice->client->phone)
                                <p class="mb-1"><strong>Phone:</strong> {{ $invoice->client->phone }}</p>
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
                                    <td class="text-end">{{ $invoice->getFormattedAmount() }}</td>
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
                                    <td class="text-end"><strong class="text-primary fs-4">{{ $invoice->getFormattedAmount() }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @if($invoice->canBePaid())
                <!-- Payment Methods Card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-credit-card me-2"></i>Choose Payment Method
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($invoice->hasPendingPayment())
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                You have a pending payment for this invoice. 
                                <a href="{{ $invoice->getPaymentUrl() }}" class="btn btn-sm btn-primary ms-2" target="_blank">
                                    Continue Payment
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-1" onclick="checkPaymentStatus()">
                                    Check Status
                                </button>
                            </div>
                        @endif

                        <form action="{{ route('payment.process', $invoice) }}" method="POST" id="paymentForm">
                            @csrf
                            <div class="row">
                                @foreach($paymentMethods as $code => $name)
                                    <div class="col-md-4 col-sm-6 mb-3">
                                        <div class="card payment-method-card" style="cursor: pointer;" onclick="selectPaymentMethod('{{ $code }}')">
                                            <div class="card-body text-center">
                                                <div class="payment-method-icon mb-2">
                                                    @switch($code)
                                                        @case('SP')
                                                            <i class="bx bx-mobile" style="font-size: 2rem; color: #ee4d2d;"></i>
                                                            @break
                                                        @case('NQ')
                                                            <i class="bx bx-qr" style="font-size: 2rem; color: #1976d2;"></i>
                                                            @break
                                                        @case('OV')
                                                            <i class="bx bx-wallet" style="font-size: 2rem; color: #4c6ef5;"></i>
                                                            @break
                                                        @case('DA')
                                                            <i class="bx bx-wallet" style="font-size: 2rem; color: #009cff;"></i>
                                                            @break
                                                        @case('LK')
                                                            <i class="bx bx-wallet" style="font-size: 2rem; color: #e74c3c;"></i>
                                                            @break
                                                        @default
                                                            <i class="bx bx-credit-card" style="font-size: 2rem; color: #6c757d;"></i>
                                                    @endswitch
                                                </div>
                                                <h6 class="mb-0">{{ $name }}</h6>
                                                <small class="text-muted">Instant Payment</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <input type="hidden" name="payment_method" id="selectedPaymentMethod">
                            
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg" id="payButton" disabled>
                                    <i class="bx bx-credit-card me-2"></i>Pay {{ $invoice->getFormattedAmount() }}
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
function selectPaymentMethod(method) {
    // Remove previous selection
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selection to clicked card
    event.currentTarget.classList.add('selected');
    
    // Set hidden input value
    document.getElementById('selectedPaymentMethod').value = method;
    
    // Enable pay button
    document.getElementById('payButton').disabled = false;
}

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
