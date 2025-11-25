@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('vendor/sneat/assets/img/icons/unicons/chart-success.png') }}" alt="Total Clients" class="rounded">
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Total Clients</span>
                    <h3 class="card-title mb-2">{{ $stats['total_clients'] }}</h3>
                    <small class="text-success fw-semibold">
                        <i class="bx bx-user"></i> Registered
                    </small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('vendor/sneat/assets/img/icons/unicons/wallet-info.png') }}" alt="Total Revenue" class="rounded">
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Total Revenue</span>
                    <h3 class="card-title mb-2">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
                    <small class="text-success fw-semibold">
                        <i class="bx bx-up-arrow-alt"></i> Paid Invoices
                    </small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('vendor/sneat/assets/img/icons/unicons/paypal.png') }}" alt="Pending Revenue" class="rounded">
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Pending Revenue</span>
                    <h3 class="card-title mb-2">Rp {{ number_format($stats['pending_revenue'], 0, ',', '.') }}</h3>
                    <small class="text-warning fw-semibold">
                        <i class="bx bx-time"></i> Unpaid Invoices
                    </small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('vendor/sneat/assets/img/icons/unicons/cc-primary.png') }}" alt="Active Services" class="rounded">
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Active Services</span>
                    <h3 class="card-title mb-2">{{ $stats['active_services'] }}</h3>
                    <small class="text-success fw-semibold">
                        <i class="bx bx-check-circle"></i> Running
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- All Invoices Management -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bx bx-receipt me-2"></i>All Invoices Management
                    </h5>
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
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Client</th>
                                    <th>Invoice</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
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
                                        <div>
                                            <h6 class="mb-0">{{ $invoice->title ?? 'Invoice #' . $invoice->id }}</h6>
                                            <small class="text-muted">{{ $invoice->description ?? 'Service Invoice' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : 'N/A' }}</small>
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
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
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
                                                <button class="dropdown-item" onclick="updateInvoiceStatus({{ $invoice->id }}, 'Unpaid')">
                                                    <i class="bx bx-time me-1 text-warning"></i> Mark as Unpaid
                                                </button>
                                                <button class="dropdown-item" onclick="updateInvoiceStatus({{ $invoice->id }}, 'Overdue')">
                                                    <i class="bx bx-clock me-1 text-danger"></i> Mark as Overdue
                                                </button>
                                                <button class="dropdown-item" onclick="updateInvoiceStatus({{ $invoice->id }}, 'Cancelled')">
                                                    <i class="bx bx-block me-1 text-secondary"></i> Cancel Invoice
                                                </button>
                                            </div>
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
                        {{ $invoices->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- All Services Management -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bx bx-package me-2"></i>All Services Management
                    </h5>
                    <div class="d-flex gap-2">
                        <select class="form-select" style="width: 150px;" id="filterServiceStatus">
                            <option value="">All Status</option>
                            <option value="Active">Active</option>
                            <option value="Suspended">Suspended</option>
                            <option value="Terminated">Terminated</option>
                            <option value="Pending">Pending</option>
                            <option value="Dibatalkan">Dibatalkan</option>
                            <option value="Disuspen">Disuspen</option>
                            <option value="Sedang Dibuat">Sedang Dibuat</option>
                            <option value="Ditutup">Ditutup</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Client</th>
                                    <th>Service</th>
                                    <th>Domain</th>
                                    <th>Price</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $service)
                                <tr>
                                    <td><span class="fw-bold text-primary">#{{ $service->id }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <img src="{{ asset('vendor/sneat/assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle">
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $service->client_name ?? 'N/A' }}</h6>
                                                <small class="text-muted">{{ $service->client_email ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-0">{{ $service->name ?? $service->product ?? 'Service' }}</h6>
                                            <small class="text-muted">{{ $service->translated_billing_cycle ?? 'Bulanan' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-primary">{{ $service->domain ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">Rp {{ number_format($service->price ?? 0, 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $service->due_date ? \Carbon\Carbon::parse($service->due_date)->format('M d, Y') : 'N/A' }}</small>
                                    </td>
                                    <td>
                                        @switch($service->status)
                                            @case('Active')
                                                <span class="badge bg-success">Active</span>
                                                @break
                                            @case('Suspended')
                                                <span class="badge bg-secondary">Suspended</span>
                                                @break
                                            @case('Terminated')
                                                <span class="badge bg-dark">Terminated</span>
                                                @break
                                            @case('Pending')
                                                <span class="badge bg-warning">Pending</span>
                                                @break
                                            @case('Dibatalkan')
                                                <span class="badge bg-danger">Dibatalkan</span>
                                                @break
                                            @case('Disuspen')
                                                <span class="badge bg-secondary">Disuspen</span>
                                                @break
                                            @case('Sedang Dibuat')
                                                <span class="badge bg-info">Sedang Dibuat</span>
                                                @break
                                            @case('Ditutup')
                                                <span class="badge bg-dark">Ditutup</span>
                                                @break
                                            @default
                                                <span class="badge bg-warning">Pending</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <button class="dropdown-item" onclick="updateServiceStatus({{ $service->id }}, 'Active')">
                                                    <i class="bx bx-check me-1 text-success"></i> Set Active
                                                </button>
                                                <button class="dropdown-item" onclick="updateServiceStatus({{ $service->id }}, 'Pending')">
                                                    <i class="bx bx-time me-1 text-warning"></i> Set Pending
                                                </button>
                                                <button class="dropdown-item" onclick="updateServiceStatus({{ $service->id }}, 'Sedang Dibuat')">
                                                    <i class="bx bx-cog me-1 text-info"></i> Sedang Dibuat
                                                </button>
                                                <div class="dropdown-divider"></div>
                                                <button class="dropdown-item" onclick="updateServiceStatus({{ $service->id }}, 'Suspended')">
                                                    <i class="bx bx-pause me-1 text-secondary"></i> Suspend
                                                </button>
                                                <button class="dropdown-item" onclick="updateServiceStatus({{ $service->id }}, 'Disuspen')">
                                                    <i class="bx bx-pause me-1 text-secondary"></i> Disuspen
                                                </button>
                                                <button class="dropdown-item" onclick="updateServiceStatus({{ $service->id }}, 'Terminated')">
                                                    <i class="bx bx-block me-1 text-dark"></i> Terminate
                                                </button>
                                                <button class="dropdown-item" onclick="updateServiceStatus({{ $service->id }}, 'Dibatalkan')">
                                                    <i class="bx bx-x me-1 text-danger"></i> Dibatalkan
                                                </button>
                                                <button class="dropdown-item" onclick="updateServiceStatus({{ $service->id }}, 'Ditutup')">
                                                    <i class="bx bx-block me-1 text-dark"></i> Ditutup
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <img src="{{ asset('vendor/sneat/assets/img/illustrations/page-misc-error-light.png') }}" alt="No services" width="150">
                                        <p class="mt-3 text-muted">No services found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($services->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $services->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update invoice status
function updateInvoiceStatus(invoiceId, status) {
    if (confirm(`Are you sure you want to mark this invoice as ${status}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/invoices/${invoiceId}/update-status`;
        form.innerHTML = `
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="${status}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Update service status
function updateServiceStatus(serviceId, status) {
    if (confirm(`Are you sure you want to set this service status to ${status}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/services/${serviceId}/update-status`;
        form.innerHTML = `
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="${status}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Filter functionality
document.getElementById('filterInvoiceStatus').addEventListener('change', function() {
    const status = this.value;
    const rows = document.querySelectorAll('#invoicesTable tbody tr');
    
    rows.forEach(row => {
        if (status === '' || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

document.getElementById('filterServiceStatus').addEventListener('change', function() {
    const status = this.value;
    const rows = document.querySelectorAll('#servicesTable tbody tr');
    
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
