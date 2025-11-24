@extends('layouts.sneat-dashboard')

@section('title', 'Client Dashboard')

@section('sidebar')
<!-- Dashboard -->
<li class="menu-item active">
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
<li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-receipt"></i>
        <div data-i18n="Account Settings">Invoices</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item">
            <a href="#" class="menu-link">
                <div data-i18n="Account">All Invoices</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="#" class="menu-link">
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
<div class="row">
    <div class="col-lg-8 mb-4 order-0">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Welcome {{ $user->name }}! 🎉</h5>
                        <p class="mb-4">
                            You have <span class="fw-bold">{{ $stats['active_services'] }}</span> active services. 
                            Check your recent activity and manage your services.
                        </p>
                        <a href="#" class="btn btn-sm btn-outline-primary">View Services</a>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img src="{{ asset('vendor/sneat/assets/img/illustrations/man-with-laptop-light.png') }}" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 order-1">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('vendor/sneat/assets/img/icons/unicons/chart-success.png') }}" alt="chart success" class="rounded" />
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Services</span>
                        <h3 class="card-title mb-2">{{ $stats['total_services'] }}</h3>
                        <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> {{ $stats['active_services'] }} Active</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-6 mb-4">
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
                            {{ $stats['unpaid_invoices'] }} unpaid
                            @if($stats['overdue_invoices'] > 0)
                                ({{ $stats['overdue_invoices'] }} overdue)
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Invoices -->
    <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div class="card-title mb-0">
                    <h5 class="m-0 me-2">Recent Invoices</h5>
                    <small class="text-muted">{{ $stats['total_invoices'] }} Total</small>
                </div>
                <div class="dropdown">
                    <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="{{ route('client.invoices.index') }}">View All Invoices</a>
                        <a class="dropdown-item" href="{{ route('client.invoices.index', ['status' => 'unpaid']) }}">Unpaid Only</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex flex-column align-items-center gap-1">
                        <h2 class="mb-2">{{ $stats['unpaid_invoices'] }}</h2>
                        <span>Unpaid</span>
                    </div>
                    @if($stats['overdue_invoices'] > 0)
                    <div class="d-flex flex-column align-items-center gap-1">
                        <h2 class="mb-2 text-danger">{{ $stats['overdue_invoices'] }}</h2>
                        <span class="text-danger">Overdue</span>
                    </div>
                    @endif
                </div>
                <ul class="p-0 m-0">
                    @forelse($invoices as $invoice)
                    <li class="d-flex mb-4 pb-1">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-{{ $invoice->status_color }}">
                                <i class="bx bx-receipt"></i>
                            </span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">
                                    <a href="{{ route('client.invoices.show', $invoice) }}" class="text-decoration-none">
                                        {{ $invoice->number }}
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    {{ $invoice->due_date->format('M d, Y') }}
                                    @if($invoice->is_overdue && $invoice->status !== 'Paid')
                                        <span class="badge bg-danger ms-1">Overdue</span>
                                    @endif
                                </small>
                            </div>
                            <div class="user-progress text-end">
                                <small class="fw-semibold">${{ number_format($invoice->total_amount, 2) }}</small>
                                <br>
                                <span class="badge bg-label-{{ $invoice->status_color }}">{{ $invoice->status }}</span>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="text-center py-3">
                        <i class="bx bx-receipt fs-1 text-muted"></i>
                        <p class="text-muted mb-0">No invoices found</p>
                        <small class="text-muted">Invoices will appear here when created</small>
                    </li>
                    @endforelse
                </ul>
                @if($invoices->count() > 0)
                <div class="text-center mt-3">
                    <a href="{{ route('client.invoices.index') }}" class="btn btn-sm btn-outline-primary">
                        View All Invoices
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Active Services -->
    <div class="col-md-6 col-lg-8 order-1 mb-4">
        <div class="card">
            <h5 class="card-header m-0 me-2 pb-3">Active Services</h5>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Price</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($services->take(5) as $service)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <img src="{{ asset('vendor/sneat/assets/img/icons/unicons/cc-primary.png') }}" alt="Service" class="rounded" />
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $service->product }}</h6>
                                            <small class="text-muted">{{ $service->domain }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold">${{ number_format($service->price, 2) }}</span>
                                    <br>
                                    <small class="text-muted">{{ $service->billing_cycle }}</small>
                                </td>
                                <td>{{ $service->due_date }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $service->status == 'Active' ? 'success' : 'warning' }} me-1">{{ $service->status }}</span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#"><i class="bx bx-edit-alt me-1"></i> Manage</a>
                                            <a class="dropdown-item" href="#"><i class="bx bx-receipt me-1"></i> View Invoice</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="py-4">
                                        <img src="{{ asset('vendor/sneat/assets/img/illustrations/page-misc-error-light.png') }}" alt="No services" width="150">
                                        <p class="mt-3 text-muted">No active services found</p>
                                        <a href="#" class="btn btn-primary">Browse Services</a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title m-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-6 mb-3">
                        <a href="#" class="btn btn-outline-primary d-flex align-items-center justify-content-center h-100 flex-column">
                            <i class="bx bx-plus fs-1 mb-2"></i>
                            <span>Order New Service</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="{{ route('client.invoices.index', ['status' => 'unpaid']) }}" class="btn btn-outline-success d-flex align-items-center justify-content-center h-100 flex-column">
                            <i class="bx bx-credit-card fs-1 mb-2"></i>
                            <span>Pay Invoices</span>
                            @if($stats['unpaid_invoices'] > 0)
                                <small class="badge bg-danger mt-1">{{ $stats['unpaid_invoices'] }}</small>
                            @endif
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="#" class="btn btn-outline-info d-flex align-items-center justify-content-center h-100 flex-column">
                            <i class="bx bx-support fs-1 mb-2"></i>
                            <span>Get Support</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="#" class="btn btn-outline-warning d-flex align-items-center justify-content-center h-100 flex-column">
                            <i class="bx bx-file fs-1 mb-2"></i>
                            <span>View Documentation</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
