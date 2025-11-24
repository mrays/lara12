@extends('layouts.sneat-dashboard')

@section('title', 'Invoice Details - ' . $invoice->number)

@section('sidebar')
<!-- Dashboard -->
<li class="menu-item">
    <a href="{{ route('client.dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div data-i18n="Analytics">Dashboard</div>
    </a>
</li>

<!-- Services -->
<li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-package"></i>
        <div data-i18n="Layouts">My Services</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item">
            <a href="#" class="menu-link">
                <div data-i18n="Without menu">Active Services</div>
            </a>
        </li>
    </ul>
</li>

<!-- Invoices -->
<li class="menu-item active">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-receipt"></i>
        <div data-i18n="Account Settings">Invoices</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item active">
            <a href="{{ route('client.invoices.index') }}" class="menu-link">
                <div data-i18n="Account">All Invoices</div>
            </a>
        </li>
    </ul>
</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Invoice Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h4 class="mb-1">{{ $invoice->number }}</h4>
                        <p class="mb-0 text-muted">{{ $invoice->title }}</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-label-{{ $invoice->status_color }} fs-6 mb-2">{{ $invoice->status }}</span>
                        <br>
                        @if($invoice->status !== 'Paid')
                            <button class="btn btn-success btn-sm" onclick="payInvoice({{ $invoice->id }})">
                                <i class="bx bx-credit-card me-1"></i> Pay Now
                            </button>
                        @endif
                        <button class="btn btn-outline-secondary btn-sm" onclick="downloadPDF()">
                            <i class="bx bx-download me-1"></i> Download PDF
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Bill To:</h6>
                        <address>
                            <strong>{{ $invoice->client->name }}</strong><br>
                            @if($invoice->client->company)
                                {{ $invoice->client->company }}<br>
                            @endif
                            @if($invoice->client->address)
                                {!! nl2br(e($invoice->client->address)) !!}<br>
                            @endif
                            {{ $invoice->client->email }}<br>
                            @if($invoice->client->phone)
                                {{ $invoice->client->phone }}
                            @endif
                        </address>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="text-muted">Invoice Details:</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-end"><strong>Issue Date:</strong></td>
                                <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-end"><strong>Due Date:</strong></td>
                                <td class="{{ $invoice->is_overdue ? 'text-danger' : '' }}">
                                    {{ $invoice->due_date->format('M d, Y') }}
                                    @if($invoice->is_overdue && $invoice->status !== 'Paid')
                                        <span class="badge bg-danger ms-1">Overdue</span>
                                    @endif
                                </td>
                            </tr>
                            @if($invoice->paid_date)
                            <tr>
                                <td class="text-end"><strong>Paid Date:</strong></td>
                                <td class="text-success">{{ $invoice->paid_date->format('M d, Y') }}</td>
                            </tr>
                            @endif
                            @if($invoice->service)
                            <tr>
                                <td class="text-end"><strong>Service:</strong></td>
                                <td>{{ $invoice->service->product }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Invoice Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                            <tr>
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ $item->description }}</h6>
                                    </div>
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end">${{ number_format($item->total_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Invoice Summary -->
        <div class="row">
            <div class="col-md-8">
                @if($invoice->description || $invoice->notes)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Additional Information</h5>
                    </div>
                    <div class="card-body">
                        @if($invoice->description)
                            <h6>Description:</h6>
                            <p>{{ $invoice->description }}</p>
                        @endif
                        
                        @if($invoice->notes)
                            <h6>Notes:</h6>
                            <p>{{ $invoice->notes }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Invoice Summary</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td>Subtotal:</td>
                                <td class="text-end">${{ number_format($invoice->subtotal, 2) }}</td>
                            </tr>
                            @if($invoice->discount_amount > 0)
                            <tr>
                                <td>Discount:</td>
                                <td class="text-end text-success">-${{ number_format($invoice->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                            @if($invoice->tax_rate > 0)
                            <tr>
                                <td>Tax ({{ $invoice->tax_rate }}%):</td>
                                <td class="text-end">${{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="table-active">
                                <td><strong>Total Amount:</strong></td>
                                <td class="text-end"><strong>${{ number_format($invoice->total_amount, 2) }}</strong></td>
                            </tr>
                        </table>
                        
                        @if($invoice->status !== 'Paid')
                            <div class="d-grid gap-2 mt-3">
                                <button class="btn btn-success" onclick="payInvoice({{ $invoice->id }})">
                                    <i class="bx bx-credit-card me-1"></i> Pay ${{ number_format($invoice->total_amount, 2) }}
                                </button>
                                @if($invoice->is_overdue)
                                    <small class="text-danger text-center">
                                        <i class="bx bx-error-circle"></i> This invoice is overdue
                                    </small>
                                @elseif($invoice->days_until_due !== null && $invoice->days_until_due <= 7)
                                    <small class="text-warning text-center">
                                        <i class="bx bx-time"></i> Due in {{ $invoice->days_until_due }} days
                                    </small>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-success mt-3">
                                <i class="bx bx-check-circle me-1"></i>
                                <strong>Paid</strong> on {{ $invoice->paid_date->format('M d, Y') }}
                                @if($invoice->payment_method)
                                    <br><small>via {{ $invoice->payment_method }}</small>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('client.invoices.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Back to Invoices
                    </a>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary" onclick="printInvoice()">
                            <i class="bx bx-printer me-1"></i> Print
                        </button>
                        <button class="btn btn-outline-secondary" onclick="downloadPDF()">
                            <i class="bx bx-download me-1"></i> Download PDF
                        </button>
                        @if($invoice->status !== 'Paid')
                            <button class="btn btn-success" onclick="payInvoice({{ $invoice->id }})">
                                <i class="bx bx-credit-card me-1"></i> Pay Now
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function payInvoice(invoiceId) {
    // Integration with payment gateway would go here
    Swal.fire({
        title: 'Payment Gateway',
        text: 'Redirecting to payment gateway...',
        icon: 'info',
        showConfirmButton: false,
        timer: 2000
    });
    
    // Example: redirect to payment URL
    // window.location.href = '/payment/invoice/' + invoiceId;
}

function downloadPDF() {
    // Generate and download PDF
    window.open('{{ route("client.invoices.show", $invoice) }}/pdf', '_blank');
}

function printInvoice() {
    window.print();
}

// Add print styles
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        @media print {
            .btn, .dropdown, .card-header .d-flex > div:last-child {
                display: none !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    `;
    document.head.appendChild(style);
});
</script>

<!-- SweetAlert2 for better alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
