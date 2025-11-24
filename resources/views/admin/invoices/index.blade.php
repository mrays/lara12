@extends('layouts.admin')
@section('title','Invoices Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="bx bx-receipt me-2"></i>Invoices Management
                        </h5>
                        <small class="text-muted">Manage all invoices and their status</small>
                    </div>
                    <div class="d-flex gap-2">
                        <select class="form-select" style="width: 150px;" id="filterInvoiceStatus">
                            <option value="">All Status</option>
                            <option value="Paid">Paid</option>
                            <option value="Unpaid">Unpaid</option>
                            <option value="Overdue">Overdue</option>
                            <option value="Cancelled">Cancelled</option>
                            <option value="Sedang Dicek">Sedang Dicek</option>
                            <option value="Lunas">Lunas</option>
                            <option value="Belum Lunas">Belum Lunas</option>
                        </select>
                        <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>New Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Client</th>
                                    <th>Due Date</th>
                                    <th>No Invoice</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                <tr>
                                    <td><span class="fw-bold text-primary">#{{ $invoice->id }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <img src="{{ asset('vendor/sneat/assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle">
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $invoice->client_name ?? 'N/A' }}</h6>
                                                <small class="text-muted">{{ $invoice->client_email ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $invoice->invoice_no ?? 'INV-' . $invoice->id }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">Rp {{ number_format($invoice->total_amount ?? $invoice->amount ?? 0, 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        @switch($invoice->status)
                                            @case('Paid')
                                                <span class="badge bg-success">Paid</span>
                                                @break
                                            @case('Unpaid')
                                                <span class="badge bg-warning">Unpaid</span>
                                                @break
                                            @case('Overdue')
                                                <span class="badge bg-danger">Overdue</span>
                                                @break
                                            @case('Cancelled')
                                                <span class="badge bg-secondary">Cancelled</span>
                                                @break
                                            @case('Sedang Dicek')
                                                <span class="badge bg-info">Sedang Dicek</span>
                                                @break
                                            @case('Lunas')
                                                <span class="badge bg-success">Lunas</span>
                                                @break
                                            @case('Belum Lunas')
                                                <span class="badge bg-warning">Belum Lunas</span>
                                                @break
                                            @default
                                                <span class="badge bg-warning">Unpaid</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <!-- View Button -->
                                            <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-sm btn-outline-info" title="View Invoice">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            
                                            <!-- Edit Button -->
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="editInvoice({{ $invoice->id }}, '{{ $invoice->title ?? '' }}', '{{ $invoice->due_date }}', '{{ $invoice->number ?? $invoice->invoice_no ?? 'INV-' . $invoice->id }}', '{{ $invoice->total_amount ?? $invoice->amount ?? 0 }}', '{{ $invoice->status }}')" 
                                                    title="Edit Invoice">
                                                <i class="bx bx-edit"></i>
                                            </button>
                                            
                                            <!-- Status Dropdown -->
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" title="Change Status">
                                                    <i class="bx bx-cog"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <h6 class="dropdown-header">Update Status</h6>
                                                    <button class="dropdown-item" onclick="updateInvoiceStatus({{ $invoice->id }}, 'Paid')">
                                                        <i class="bx bx-check me-1 text-success"></i> Mark as Paid
                                                    </button>
                                                    <button class="dropdown-item" onclick="updateInvoiceStatus({{ $invoice->id }}, 'Lunas')">
                                                        <i class="bx bx-check me-1 text-success"></i> Mark as Lunas
                                                    </button>
                                                    <button class="dropdown-item" onclick="updateInvoiceStatus({{ $invoice->id }}, 'Sedang Dicek')">
                                                        <i class="bx bx-time me-1 text-info"></i> Sedang Dicek
                                                    </button>
                                                    <button class="dropdown-item" onclick="updateInvoiceStatus({{ $invoice->id }}, 'Belum Lunas')">
                                                        <i class="bx bx-x me-1 text-warning"></i> Belum Lunas
                                                    </button>
                                                    <div class="dropdown-divider"></div>
                                                    <button class="dropdown-item" onclick="updateInvoiceStatus({{ $invoice->id }}, 'Overdue')">
                                                        <i class="bx bx-clock me-1 text-danger"></i> Mark as Overdue
                                                    </button>
                                                    <button class="dropdown-item" onclick="updateInvoiceStatus({{ $invoice->id }}, 'Cancelled')">
                                                        <i class="bx bx-block me-1 text-secondary"></i> Cancel Invoice
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Delete Button -->
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteInvoice({{ $invoice->id }})" 
                                                    title="Delete Invoice">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <img src="{{ asset('vendor/sneat/assets/img/illustrations/page-misc-error-light.png') }}" alt="No invoices" width="150">
                                        <p class="mt-3 text-muted">No invoices found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($invoices->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $invoices->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Invoice Modal -->
<div class="modal fade" id="editInvoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-edit me-2"></i>Edit Invoice
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editInvoiceForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="edit_title" class="form-label">Invoice Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_due_date" class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="edit_due_date" name="due_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_invoice_no" class="form-label">No Invoice</label>
                            <input type="text" class="form-control" id="edit_invoice_no" name="invoice_no" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_amount" class="form-label">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="edit_amount" name="amount" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>Update Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Edit invoice function
function editInvoice(invoiceId, title, dueDate, invoiceNo, amount, status) {
    document.getElementById('editInvoiceForm').action = `/admin/invoices/${invoiceId}/quick-update`;
    document.getElementById('edit_title').value = title || '';
    document.getElementById('edit_due_date').value = dueDate ? dueDate.split(' ')[0] : '';
    document.getElementById('edit_invoice_no').value = invoiceNo;
    document.getElementById('edit_amount').value = amount;
    document.getElementById('edit_status').value = status;
    
    new bootstrap.Modal(document.getElementById('editInvoiceModal')).show();
}

// Update invoice status function
function updateInvoiceStatus(invoiceId, status) {
    if (confirm(`Are you sure you want to mark this invoice as ${status}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/invoices/${invoiceId}/status`;
        form.innerHTML = `
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="${status}">
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

// Filter functionality
document.getElementById('filterInvoiceStatus').addEventListener('change', function() {
    const status = this.value;
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (status === '' || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
@endsection
