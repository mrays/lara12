@extends('layouts.sneat-dashboard')

@section('title', 'My Invoices')

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
        <li class="menu-item">
            <a href="#" class="menu-link">
                <div data-i18n="Without navbar">Service History</div>
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
        <li class="menu-item">
            <a href="{{ route('client.invoices.index', ['status' => 'unpaid']) }}" class="menu-link">
                <div data-i18n="Notifications">Unpaid Invoices</div>
            </a>
        </li>
    </ul>
</li>

<!-- Support -->
<li class="menu-item">
    <a href="#" class="menu-link">
        <i class="menu-icon tf-icons bx bx-support"></i>
        <div data-i18n="Support">Support</div>
    </a>
</li>
@endsection

@section('content')
<!-- Invoice Statistics -->
<div class="row mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <img src="{{ asset('vendor/sneat/assets/img/icons/unicons/chart.png') }}" alt="chart" class="rounded" />
                    </div>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                            <a class="dropdown-item" href="{{ route('client.invoices.index') }}">View All</a>
                        </div>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Total Invoices</span>
                <h3 class="card-title mb-2">{{ $stats['total'] }}</h3>
                <small class="text-muted fw-semibold">All time invoices</small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <img src="{{ asset('vendor/sneat/assets/img/icons/unicons/wallet-info.png') }}" alt="Credit Card" class="rounded" />
                    </div>
                </div>
                <span>Unpaid Amount</span>
                <h3 class="card-title text-nowrap mb-1">${{ number_format($stats['unpaid_amount'], 2) }}</h3>
                <small class="text-danger fw-semibold">
                    <i class="bx bx-down-arrow-alt"></i> 
                    {{ $stats['unpaid'] }} invoices
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <img src="{{ asset('vendor/sneat/assets/img/icons/unicons/paypal.png') }}" alt="paypal" class="rounded" />
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Paid Invoices</span>
                <h3 class="card-title mb-2">{{ $stats['paid'] }}</h3>
                <small class="text-success fw-semibold">
                    <i class="bx bx-up-arrow-alt"></i> 
                    Successfully paid
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <img src="{{ asset('vendor/sneat/assets/img/icons/unicons/cc-warning.png') }}" alt="Credit Card" class="rounded" />
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Overdue</span>
                <h3 class="card-title mb-2">{{ $stats['overdue'] }}</h3>
                <small class="text-warning fw-semibold">
                    <i class="bx bx-time-five"></i> 
                    Need attention
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('client.invoices.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Sent" {{ request('status') == 'Sent' ? 'selected' : '' }}>Sent</option>
                    <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Overdue" {{ request('status') == 'Overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Invoice number, title..." value="{{ request('search') }}">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bx bx-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Invoices Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">My Invoices</h5>
        <div class="d-flex gap-2">
            <a href="{{ route('client.invoices.index', ['status' => 'unpaid']) }}" class="btn btn-sm btn-outline-warning">
                <i class="bx bx-time"></i> Unpaid ({{ $stats['unpaid'] }})
            </a>
            <a href="{{ route('client.invoices.index', ['status' => 'overdue']) }}" class="btn btn-sm btn-outline-danger">
                <i class="bx bx-error"></i> Overdue ({{ $stats['overdue'] }})
            </a>
        </div>
    </div>
    
    <div class="card-body">
        @if($invoices->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Service</th>
                        <th>Amount</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded bg-label-{{ $invoice->status_color }}">
                                        <i class="bx bx-receipt"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $invoice->number }}</h6>
                                    <small class="text-muted">{{ $invoice->title }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($invoice->service)
                                <span class="fw-semibold">{{ $invoice->service->product }}</span>
                                @if($invoice->service->domain)
                                    <br><small class="text-muted">{{ $invoice->service->domain }}</small>
                                @endif
                            @else
                                <span class="text-muted">No service linked</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-semibold">${{ number_format($invoice->total_amount, 2) }}</span>
                        </td>
                        <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                        <td>
                            <span class="{{ $invoice->is_overdue ? 'text-danger' : '' }}">
                                {{ $invoice->due_date->format('M d, Y') }}
                            </span>
                            @if($invoice->is_overdue && $invoice->status !== 'Paid')
                                <br><small class="text-danger">
                                    <i class="bx bx-error-circle"></i> Overdue
                                </small>
                            @elseif($invoice->days_until_due !== null && $invoice->days_until_due <= 7 && $invoice->days_until_due > 0)
                                <br><small class="text-warning">
                                    <i class="bx bx-time"></i> Due in {{ $invoice->days_until_due }} days
                                </small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-label-{{ $invoice->status_color }} me-1">
                                {{ $invoice->status }}
                            </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('client.invoices.show', $invoice) }}">
                                        <i class="bx bx-show me-1"></i> View Details
                                    </a>
                                    @if($invoice->canBePaid())
                                        <a class="dropdown-item text-success" href="{{ route('payment.show', $invoice) }}">
                                            <i class="bx bx-credit-card me-1"></i> Pay Now
                                        </a>
                                    @elseif($invoice->hasPendingPayment())
                                        <a class="dropdown-item text-warning" href="{{ $invoice->getPaymentUrl() }}" target="_blank">
                                            <i class="bx bx-time me-1"></i> Continue Payment
                                        </a>
                                    @endif
                                    <a class="dropdown-item" href="#" onclick="downloadInvoice({{ $invoice->id }})">
                                        <i class="bx bx-download me-1"></i> Download PDF
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
                <small class="text-muted">
                    Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} results
                </small>
            </div>
            <div>
                {{ $invoices->links() }}
            </div>
        </div>
        
        @else
        <div class="text-center py-5">
            <img src="{{ asset('vendor/sneat/assets/img/illustrations/page-misc-error-light.png') }}" alt="No invoices" width="200">
            <h5 class="mt-3">No invoices found</h5>
            <p class="text-muted">You don't have any invoices yet. When you have active services, invoices will appear here.</p>
            <a href="{{ route('client.dashboard') }}" class="btn btn-primary">
                <i class="bx bx-arrow-back"></i> Back to Dashboard
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function payInvoice(invoiceId) {
    // This would integrate with your payment gateway
    alert('Payment integration would be implemented here for invoice ID: ' + invoiceId);
}

function downloadInvoice(invoiceId) {
    // This would generate and download PDF
    window.open('/client/invoices/' + invoiceId + '/pdf', '_blank');
}

// Auto-refresh overdue status
setInterval(function() {
    // You could implement real-time updates here
}, 60000); // Check every minute
</script>
@endpush
