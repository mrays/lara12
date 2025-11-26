@extends('layouts.sneat-dashboard')

@section('title', 'Invoice Details - ' . $invoice->number)

@section('content')
<div class="row invoice-preview">
    <!-- Invoice -->
    <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
        <div class="card invoice-preview-card">
            <div class="card-body">
                <!-- Header: Logo & Invoice Info -->
                <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column p-sm-3 p-0">
                    <div class="mb-xl-0 mb-4">
                        <div class="d-flex svg-illustration mb-3 gap-2">
                            <img src="{{ asset(config('company.logo.main', 'images/logoweb.png')) }}" 
                                 alt="{{ config('company.name') }}" 
                                 style="height: 40px; max-width: 150px; object-fit: contain;">
                            <span class="app-brand-text demo text-body fw-bold fs-4">{{ config('company.name') }}</span>
                        </div>
                        <p class="mb-1">{{ config('company.address.street') }}</p>
                        <p class="mb-1">{{ config('company.address.city') }}, {{ config('company.address.state') }} {{ config('company.address.postal_code') }}</p>
                        <p class="mb-1">{{ config('company.address.country') }}</p>
                        <p class="mb-0">Phone: {{ config('company.phone') }}</p>
                        <p class="mb-0">Email: {{ config('company.email') }}</p>
                    </div>
                    <div>
                        <h4 class="text-primary">Invoice #{{ $invoice->number }}</h4>
                        <div class="mb-2">
                            <span class="me-1">Date Issues:</span>
                            <span class="fw-medium">{{ $invoice->issue_date ? $invoice->issue_date->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="me-1">Date Due:</span>
                            <span class="fw-medium {{ $invoice->status == 'Overdue' ? 'text-danger' : '' }}">
                                {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-0">
            
            <div class="card-body">
                <!-- Invoice To & Bill To -->
                <div class="row p-sm-3 p-0">
                    <div class="col-xl-6 col-md-12 col-sm-5 col-12 mb-xl-0 mb-md-4 mb-sm-0 mb-4">
                        <h6 class="pb-2">Invoice To:</h6>
                        <p class="mb-1 fw-medium">{{ $invoice->client->name ?? 'N/A' }}</p>
                        @if($invoice->client->business_name ?? null)
                            <p class="mb-1">{{ $invoice->client->business_name }}</p>
                        @endif
                        @if($invoice->client->address ?? null)
                            <p class="mb-1">{!! nl2br(e($invoice->client->address)) !!}</p>
                        @endif
                        <p class="mb-1">{{ $invoice->client->phone ?? '' }}</p>
                        <p class="mb-0">{{ $invoice->client->email ?? 'N/A' }}</p>
                    </div>
                    <div class="col-xl-6 col-md-12 col-sm-7 col-12">
                        <h6 class="pb-2">Bill To:</h6>
                        <table>
                            <tbody>
                                <tr>
                                    <td class="pe-3">Total Due:</td>
                                    <td class="fw-medium">{{ $invoice->formatted_total }}</td>
                                </tr>
                                @if($invoice->service)
                                <tr>
                                    <td class="pe-3">Service:</td>
                                    <td>{{ $invoice->service->product }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="pe-3">Status:</td>
                                    <td>
                                        <span class="badge bg-label-{{ $invoice->status_color }}">{{ $invoice->status }}</span>
                                    </td>
                                </tr>
                                @if($invoice->paid_date)
                                <tr>
                                    <td class="pe-3">Paid Date:</td>
                                    <td class="text-success">{{ $invoice->paid_date->format('M d, Y') }}</td>
                                </tr>
                                @endif
                                @if($invoice->payment_method ?? null)
                                <tr>
                                    <td class="pe-3">Payment:</td>
                                    <td>{{ $invoice->payment_method }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Invoice Items Table -->
            <div class="table-responsive">
                <table class="table border-top m-0">
                    <thead>
                        <tr>
                            <th>ITEM</th>
                            <th>DESCRIPTION</th>
                            <th class="text-end">COST</th>
                            <th class="text-center">QTY</th>
                            <th class="text-end">PRICE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($invoice->items) && count($invoice->items) > 0)
                            @foreach($invoice->items as $item)
                            <tr>
                                <td class="text-nowrap">{{ $item->description }}</td>
                                <td class="text-nowrap">{{ $item->notes ?? '-' }}</td>
                                <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-nowrap">{{ $invoice->title ?? 'Service Invoice' }}</td>
                                <td class="text-nowrap">{{ $invoice->description ?? 'Invoice for services' }}</td>
                                <td class="text-end">{{ $invoice->formatted_total }}</td>
                                <td class="text-center">1</td>
                                <td class="text-end">{{ $invoice->formatted_total }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <div class="card-body">
                <div class="row p-sm-3 p-0">
                    <div class="col-md-6 mb-md-0 mb-3">
                        <div class="d-flex align-items-center mb-3">
                            <span class="me-2 fw-medium">Salesperson:</span>
                            <span>{{ config('company.name') }}</span>
                        </div>
                        <span>Thanks for your business</span>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end">
                            <table>
                                <tbody>
                                    <tr>
                                        <td class="pe-4">Subtotal:</td>
                                        <td class="text-end fw-medium">{{ $invoice->formatted_subtotal }}</td>
                                    </tr>
                                    @if(($invoice->discount_amount ?? 0) > 0)
                                    <tr>
                                        <td class="pe-4">Discount:</td>
                                        <td class="text-end text-success">-Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    @if(($invoice->tax_rate ?? 0) > 0)
                                    <tr>
                                        <td class="pe-4">Tax ({{ $invoice->tax_rate }}%):</td>
                                        <td class="text-end">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="pe-4 fw-medium">Total:</td>
                                        <td class="text-end fw-bold text-primary fs-5">{{ $invoice->formatted_total }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-0">
            
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <span class="fw-medium">Note:</span>
                        <span>{{ $invoice->notes ?? 'It was a pleasure working with you and your team. We hope you will keep us in mind for future projects. Thank You!' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Invoice -->

    <!-- Invoice Actions -->
    <div class="col-xl-3 col-md-4 col-12 invoice-actions">
        <div class="card">
            <div class="card-body">
                @if(in_array($invoice->status, ['Unpaid', 'gagal', 'Overdue']))
                    <button class="btn btn-success d-grid w-100 mb-3" onclick="payInvoice({{ $invoice->id }})">
                        <span class="d-flex align-items-center justify-content-center text-nowrap">
                            <i class="bx bx-credit-card bx-xs me-1"></i>Pay {{ $invoice->formatted_total }}
                        </span>
                    </button>
                    @if($invoice->status == 'Overdue')
                        <div class="alert alert-danger py-2 mb-3">
                            <small><i class="bx bx-error-circle"></i> This invoice is overdue</small>
                        </div>
                    @endif
                @else
                    <div class="alert alert-success py-2 mb-3">
                        <small>
                            <i class="bx bx-check-circle"></i> <strong>Paid</strong> on {{ $invoice->paid_date->format('M d, Y') }}
                            @if($invoice->payment_method ?? null)
                                <br>via {{ $invoice->payment_method }}
                            @endif
                        </small>
                    </div>
                @endif
                
                <button class="btn btn-outline-secondary d-grid w-100 mb-3" onclick="downloadPDF()">
                    <span class="d-flex align-items-center justify-content-center text-nowrap">
                        <i class="bx bx-download bx-xs me-1"></i>Download PDF
                    </span>
                </button>
                
                <button class="btn btn-outline-secondary d-grid w-100 mb-3" onclick="printInvoice()">
                    <span class="d-flex align-items-center justify-content-center text-nowrap">
                        <i class="bx bx-printer bx-xs me-1"></i>Print
                    </span>
                </button>
                
                <a href="{{ route('client.invoices.index') }}" class="btn btn-outline-primary d-grid w-100">
                    <span class="d-flex align-items-center justify-content-center text-nowrap">
                        <i class="bx bx-arrow-back bx-xs me-1"></i>Back to Invoices
                    </span>
                </a>
            </div>
        </div>
    </div>
    <!-- /Invoice Actions -->
</div>

<!-- Print Styles -->
<style>
@media print {
    .invoice-actions, 
    .layout-menu,
    .layout-navbar,
    .content-footer,
    .btn {
        display: none !important;
    }
    .invoice-preview-card {
        border: none !important;
        box-shadow: none !important;
    }
    .col-xl-9 {
        width: 100% !important;
        max-width: 100% !important;
    }
}
</style>
@endsection

@push('scripts')
<script>
function payInvoice(invoiceId) {
    if (confirm('Proceed to payment for this invoice?')) {
        window.location.href = `/client/invoices/${invoiceId}/pay`;
    }
}

function downloadPDF() {
    window.open('{{ route("client.invoices.pdf", $invoice->id) }}', '_blank');
}

function printInvoice() {
    window.print();
}
</script>

<!-- SweetAlert2 for better alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
