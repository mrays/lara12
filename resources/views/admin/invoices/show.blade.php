@extends('layouts.admin')

@section('title', 'Invoice Details - ' . ($invoice->number ?? 'N/A'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="tf-icons bx bx-receipt me-2"></i>Invoice Details
                        </h5>
                        <small class="text-muted">Invoice #{{ $invoice->number ?? 'N/A' }}</small>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.invoices.index') }}">Invoices</a></li>
                            <li class="breadcrumb-item active">Invoice #{{ $invoice->number ?? 'N/A' }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Content -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Invoice Header -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4 class="text-primary mb-3">
                                <i class="tf-icons bx bx-building me-2"></i>Exputra Cloud
                            </h4>
                            <p class="mb-1">Jl. Teknologi No. 123</p>
                            <p class="mb-1">Jakarta, Indonesia 12345</p>
                            <p class="mb-1">Phone: +62 21 1234 5678</p>
                            <p class="mb-0">Email: info@exputra.cloud</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h2 class="text-primary mb-3">INVOICE</h2>
                            <div class="mb-2">
                                <strong>Invoice Number:</strong><br>
                                <span class="fs-5">{{ $invoice->number ?? 'N/A' }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Issue Date:</strong><br>
                                {{ $invoice->issue_date ? date('M d, Y', strtotime($invoice->issue_date)) : ($invoice->created_at ? $invoice->created_at->format('M d, Y') : 'N/A') }}
                            </div>
                            <div class="mb-2">
                                <strong>Due Date:</strong><br>
                                {{ $invoice->due_date ? date('M d, Y', strtotime($invoice->due_date)) : 'N/A' }}
                            </div>
                            <div>
                                <strong>Status:</strong><br>
                                @switch($invoice->status)
                                    @case('Paid')
                                    @case('Lunas')
                                        <span class="badge bg-success fs-6">{{ $invoice->status }}</span>
                                        @break
                                    @case('Unpaid')
                                    @case('Belum Lunas')
                                        <span class="badge bg-warning fs-6">{{ $invoice->status }}</span>
                                        @break
                                    @case('Overdue')
                                        <span class="badge bg-danger fs-6">{{ $invoice->status }}</span>
                                        @break
                                    @case('Sedang Dicek')
                                        <span class="badge bg-info fs-6">{{ $invoice->status }}</span>
                                        @break
                                    @case('Cancelled')
                                        <span class="badge bg-secondary fs-6">{{ $invoice->status }}</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary fs-6">{{ $invoice->status }}</span>
                                @endswitch
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Client Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="tf-icons bx bx-user me-2"></i>Bill To:
                            </h6>
                            @php
                                $client = \DB::table('users')->where('id', $invoice->client_id)->first();
                            @endphp
                            @if($client)
                                <div class="mb-2">
                                    <strong>{{ $client->name }}</strong>
                                </div>
                                <div class="mb-1">{{ $client->email }}</div>
                                @if($client->phone)
                                    <div class="mb-1">{{ $client->phone }}</div>
                                @endif
                                @if($client->address)
                                    <div class="mb-1">{{ $client->address }}</div>
                                @endif
                            @else
                                <div class="text-muted">Client information not available</div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($invoice->paid_date)
                                <div class="alert alert-success">
                                    <h6 class="alert-heading mb-2">
                                        <i class="tf-icons bx bx-check-circle me-2"></i>Payment Information
                                    </h6>
                                    <p class="mb-1"><strong>Paid Date:</strong> {{ date('M d, Y', strtotime($invoice->paid_date)) }}</p>
                                    @if($invoice->payment_method)
                                        <p class="mb-1"><strong>Payment Method:</strong> {{ $invoice->payment_method }}</p>
                                    @endif
                                    @if($invoice->payment_reference)
                                        <p class="mb-0"><strong>Reference:</strong> {{ $invoice->payment_reference }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Invoice Items -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="tf-icons bx bx-list-ul me-2"></i>Invoice Details:
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Description</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Unit Price</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <strong>{{ $invoice->title ?? 'Service Payment' }}</strong>
                                                @if($invoice->description)
                                                    <br><small class="text-muted">{{ $invoice->description }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">1</td>
                                            <td class="text-end">Rp {{ number_format($invoice->total_amount ?? 0, 0, ',', '.') }}</td>
                                            <td class="text-end">Rp {{ number_format($invoice->total_amount ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        @if($invoice->subtotal && $invoice->subtotal != $invoice->total_amount)
                                            <tr>
                                                <th colspan="3" class="text-end">Subtotal:</th>
                                                <th class="text-end">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</th>
                                            </tr>
                                        @endif
                                        @if($invoice->tax_amount && $invoice->tax_amount > 0)
                                            <tr>
                                                <th colspan="3" class="text-end">Tax ({{ $invoice->tax_rate ?? 0 }}%):</th>
                                                <th class="text-end">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</th>
                                            </tr>
                                        @endif
                                        @if($invoice->discount_amount && $invoice->discount_amount > 0)
                                            <tr>
                                                <th colspan="3" class="text-end">Discount:</th>
                                                <th class="text-end text-success">-Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</th>
                                            </tr>
                                        @endif
                                        <tr class="table-primary">
                                            <th colspan="3" class="text-end fs-5">Total Amount:</th>
                                            <th class="text-end fs-5">Rp {{ number_format($invoice->total_amount ?? 0, 0, ',', '.') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($invoice->notes)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="tf-icons bx bx-note me-2"></i>Notes:
                                </h6>
                                <div class="alert alert-light">
                                    {{ $invoice->notes }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-between flex-wrap gap-2">
                                <div>
                                    <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">
                                        <i class="tf-icons bx bx-arrow-back me-1"></i>Back to Invoices
                                    </a>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button class="btn btn-outline-primary" onclick="editInvoice({{ $invoice->id }}, '{{ $invoice->title ?? '' }}', '{{ $invoice->due_date }}', '{{ $invoice->number }}', '{{ $invoice->total_amount ?? 0 }}', '{{ $invoice->status }}')">
                                        <i class="tf-icons bx bx-edit me-1"></i>Edit Invoice
                                    </button>
                                    @if(!in_array($invoice->status, ['Paid', 'Lunas']))
                                        <button class="btn btn-success" onclick="updateInvoiceStatus({{ $invoice->id }}, 'Paid')">
                                            <i class="tf-icons bx bx-check me-1"></i>Mark as Paid
                                        </button>
                                    @endif
                                    <button class="btn btn-info" onclick="window.print()">
                                        <i class="tf-icons bx bx-printer me-1"></i>Print
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteInvoice({{ $invoice->id }})">
                                        <i class="tf-icons bx bx-trash me-1"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Invoice Modal (reuse from index page) -->
<div class="modal fade" id="editInvoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="tf-icons bx bx-edit me-2"></i>Edit Invoice
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editInvoiceForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Invoice Title</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="edit_due_date" name="due_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_invoice_no" class="form-label">Invoice Number</label>
                        <input type="text" class="form-control" id="edit_invoice_no" name="invoice_no" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="edit_amount" name="amount" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="Draft">Draft</option>
                            <option value="Sent">Sent</option>
                            <option value="Paid">Paid</option>
                            <option value="Overdue">Overdue</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="tf-icons bx bx-save me-1"></i>Update Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Edit invoice function
function editInvoice(invoiceId, title, dueDate, invoiceNo, amount, status) {
    document.getElementById('edit_title').value = title || '';
    document.getElementById('edit_due_date').value = dueDate;
    document.getElementById('edit_invoice_no').value = invoiceNo;
    document.getElementById('edit_amount').value = amount;
    document.getElementById('edit_status').value = status;
    document.getElementById('editInvoiceForm').action = `/admin/invoices/${invoiceId}/quick-update`;
    
    const modal = new bootstrap.Modal(document.getElementById('editInvoiceModal'));
    modal.show();
}

// Update invoice status function
function updateInvoiceStatus(invoiceId, newStatus) {
    if (confirm(`Are you sure you want to mark this invoice as ${newStatus}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/invoices/${invoiceId}/status`;
        form.innerHTML = `
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="${newStatus}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Delete invoice function
function deleteInvoice(invoiceId) {
    if (confirm('Are you sure you want to delete this invoice? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/invoices/${invoiceId}/delete`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Print styles
@media print {
    .btn, .breadcrumb, .card-header nav, .d-flex.justify-content-between {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .card-body, .card-body * {
        visibility: visible;
    }
    .card-body {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>
@endsection
