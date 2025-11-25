@extends('layouts.sneat-dashboard')

@section('title', 'Manage Service - ' . ($service->product ?? 'Service'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('client.services.index') }}">My Services</a>
            </li>
            <li class="breadcrumb-item active">{{ $service->product ?? 'Service' }}</li>
        </ol>
    </nav>

    <!-- Service Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-wrapper me-3">
                                <div class="avatar avatar-lg">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        <i class="bx bx-globe fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div>
                                <h4 class="mb-1">{{ $service->product ?? 'Business Website Exclusive Type M' }}</h4>
                                <p class="mb-0 text-muted">{{ $service->domain ?? 'websiteload' }}</p>
                            </div>
                        </div>
                        <div class="text-end">
                            @switch($service->status)
                                @case('Active')
                                    <span class="badge bg-label-success fs-6 mb-2">ACTIVE</span>
                                    @break
                                @case('Suspended')
                                    <span class="badge bg-label-warning fs-6 mb-2">SUSPENDED</span>
                                    @break
                                @case('Terminated')
                                    <span class="badge bg-label-danger fs-6 mb-2">TERMINATED</span>
                                    @break
                                @default
                                    <span class="badge bg-label-secondary fs-6 mb-2">{{ strtoupper($service->status) }}</span>
                            @endswitch
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Management Tabs -->
    <div class="row">
        <div class="col-md-3">
            <!-- Sidebar Menu -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="nav-align-left">
                        <ul class="nav nav-pills flex-column" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active w-100 text-start" 
                                        id="overview-tab" data-bs-toggle="pill" 
                                        data-bs-target="#overview" type="button" 
                                        role="tab" aria-controls="overview" aria-selected="true">
                                    <i class="bx bx-star me-2"></i>Overview
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link w-100 text-start" 
                                        id="information-tab" data-bs-toggle="pill" 
                                        data-bs-target="#information" type="button" 
                                        role="tab" aria-controls="information" aria-selected="false">
                                    <i class="bx bx-info-circle me-2"></i>Information
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link w-100 text-start" 
                                        id="actions-tab" data-bs-toggle="pill" 
                                        data-bs-target="#actions" type="button" 
                                        role="tab" aria-controls="actions" aria-selected="false">
                                    <i class="bx bx-cog me-2"></i>Actions
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Service Categories -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">LAYANAN</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bx bx-globe me-2"></i>Website</span>
                            <span class="badge bg-primary rounded-pill">{{ $service->status === 'Active' ? '1' : '0' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bx bx-dots-horizontal me-2"></i>Coming Soon</span>
                            <span class="badge bg-secondary rounded-pill">0</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Billing -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">BILLING</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="{{ route('client.invoices.index') }}" class="text-decoration-none">
                                <i class="bx bx-receipt me-2"></i>Invoices
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Support -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">SUPPORT</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="#" class="text-decoration-none" onclick="contactSupport()">
                                <i class="bx bx-message-dots me-2"></i>WhatsApp Kami
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ $service->product ?? 'Business Website Exclusive Type M' }}</h5>
                        </div>
                        <div class="card-body">
                            <!-- Service Details -->
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold text-muted">Username</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $service->username ?? 'BININOK' }}" readonly>
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $service->username ?? 'BININOK' }}')">
                                            <i class="bx bx-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold text-muted">Password</label>
                                    <div class="input-group">
                                        <input type="password" id="password-field" class="form-control" value="{{ $service->password ?? 'booyofS*w&*DN' }}" readonly>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility()">
                                            <i class="bx bx-show" id="password-toggle-icon"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $service->password ?? 'booyofS*w&*DN' }}')">
                                            <i class="bx bx-copy"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold text-muted">Server</label>
                                    <input type="text" class="form-control" value="{{ $service->server ?? 'Default Server' }}" readonly>
                                </div>
                            </div>

                            <!-- Upgrade Request Status -->
                            @php
                                $pendingUpgradeRequest = \App\Models\ServiceUpgradeRequest::where('service_id', $service->id)
                                    ->where('client_id', auth()->id())
                                    ->whereIn('status', ['pending', 'approved', 'processing'])
                                    ->first();
                            @endphp
                            
                            @if($pendingUpgradeRequest)
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="alert alert-{{ $pendingUpgradeRequest->status === 'pending' ? 'warning' : ($pendingUpgradeRequest->status === 'approved' ? 'success' : 'info') }} d-flex align-items-start">
                                            <i class="bx bx-{{ $pendingUpgradeRequest->status === 'pending' ? 'time' : ($pendingUpgradeRequest->status === 'approved' ? 'check-circle' : 'cog') }} me-2 mt-1"></i>
                                            <div class="flex-grow-1">
                                                <strong>Upgrade Request Status:</strong>
                                                <div class="mt-2">
                                                    <span class="badge bg-{{ $pendingUpgradeRequest->status === 'pending' ? 'warning' : ($pendingUpgradeRequest->status === 'approved' ? 'success' : 'info') }} me-2">
                                                        {{ ucfirst($pendingUpgradeRequest->status) }}
                                                    </span>
                                                    <small class="text-muted">
                                                        Request #{{ $pendingUpgradeRequest->id }} submitted on {{ $pendingUpgradeRequest->created_at->format('M d, Y') }}
                                                    </small>
                                                </div>
                                                <div class="mt-2">
                                                    <strong>From:</strong> {{ $pendingUpgradeRequest->current_plan }} 
                                                    <i class="bx bx-right-arrow-alt mx-2"></i>
                                                    <strong>To:</strong> {{ $pendingUpgradeRequest->requested_plan }}
                                                </div>
                                                
                                                @if($pendingUpgradeRequest->status === 'approved')
                                                    <div class="mt-2 text-success">
                                                        <i class="bx bx-check-circle me-1"></i>
                                                        Your upgrade request has been approved! 
                                                        <a href="{{ route('client.invoices.index') }}" class="alert-link">Check your invoices</a> for payment details.
                                                    </div>
                                                @elseif($pendingUpgradeRequest->status === 'processing')
                                                    <div class="mt-2 text-info">
                                                        <i class="bx bx-cog me-1"></i>
                                                        Your upgrade request is currently being processed by our team.
                                                    </div>
                                                @else
                                                    <div class="mt-2 text-warning">
                                                        <i class="bx bx-time me-1"></i>
                                                        Your upgrade request is pending admin approval.
                                                    </div>
                                                @endif
                                                
                                                @if($pendingUpgradeRequest->admin_notes)
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            <strong>Admin Notes:</strong> {{ $pendingUpgradeRequest->admin_notes }}
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    @if($service->status === 'Active')
                                        <button class="btn btn-primary me-2" onclick="loginDashboard('{{ $service->domain }}')">
                                            <i class="bx bx-log-in me-1"></i>Login Dashboard
                                        </button>
                                        <button class="btn btn-success" onclick="contactSupport()">
                                            <i class="bx bx-message-dots me-1"></i>Hubungi Kami
                                        </button>
                                    @else
                                        <div class="alert alert-warning d-flex align-items-center mb-3">
                                            <i class="bx bx-info-circle me-2"></i>
                                            <span>Service is currently {{ strtolower($service->status) }}. Please contact support for assistance.</span>
                                        </div>
                                        <button class="btn btn-warning" onclick="contactSupport()">
                                            <i class="bx bx-phone me-1"></i>Contact Support
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Tips Section -->
                            <div class="alert alert-primary d-flex align-items-start mb-4">
                                <i class="bx bx-info-circle me-2 mt-1"></i>
                                <div>
                                    <strong>Tips:</strong> Silakan klik tombol <strong>"Login Dashboard"</strong> untuk masuk ke Dashboard Website
                                </div>
                            </div>

                            <!-- Current Plan Section -->
                            <div class="card border">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="mb-2">Current Plan</h6>
                                            <h5 class="mb-1">Your Current Plan is {{ $service->product ?? 'Basic' }}</h5>
                                            <p class="text-muted mb-3">
                                                @if($service->billing_cycle === 'yearly' || $service->billing_cycle === 'annually')
                                                    Annual subscription with better value
                                                @else
                                                    Monthly subscription plan
                                                @endif
                                            </p>
                                            
                                            <div class="mb-3">
                                                <small class="text-muted">Active until {{ $service->due_date ? $service->due_date->format('M d, Y') : 'Dec 09, 2021' }}</small><br>
                                                <small class="text-muted">We will send you a notification upon Subscription expiration</small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <span class="badge bg-label-primary me-2">
                                                    {{ $service->price ? 'Rp ' . number_format($service->price, 0, ',', '.') : 'Rp 199.000' }} 
                                                    @if($service->billing_cycle === 'yearly' || $service->billing_cycle === 'annually')
                                                        Per Year
                                                    @elseif($service->billing_cycle === 'monthly')
                                                        Per Month
                                                    @else
                                                        Per {{ ucfirst($service->billing_cycle ?? 'Month') }}
                                                    @endif
                                                </span>
                                                <span class="badge bg-label-info">Popular</span>
                                            </div>
                                            
                                            <p class="text-muted mb-3">
                                                @if($service->billing_cycle === 'yearly' || $service->billing_cycle === 'annually')
                                                    Annual plan with cost savings for long-term commitment
                                                @else
                                                    Flexible monthly plan for small to medium businesses
                                                @endif
                                            </p>
                                            
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-primary" onclick="upgradePlan()">
                                                    <i class="bx bx-up-arrow-alt me-1"></i>Upgrade Plan
                                                </button>
                                                <button class="btn btn-outline-danger" onclick="cancelSubscription()">
                                                    Cancel Subscription
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center justify-content-center h-100">
                                                <div class="text-center">
                                                    <p class="text-muted mb-3">Manage your subscription and upgrade anytime</p>
                                                    
                                                    <div class="mb-3">
                                                        <h6 class="mb-1">Service Status</h6>
                                                        <div class="progress mb-2" style="height: 8px;">
                                                            <div class="progress-bar bg-{{ $service->status == 'Active' ? 'success' : 'warning' }}" role="progressbar" style="width: 100%"></div>
                                                        </div>
                                                        <small class="text-muted">Your service is {{ $service->status }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Information Tab -->
                <div class="tab-pane fade" id="information" role="tabpanel" aria-labelledby="information-tab">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Service Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Service Details</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Service Name:</strong></td>
                                            <td>{{ $service->product ?? 'Service' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Product:</strong></td>
                                            <td>{{ $service->product }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Domain:</strong></td>
                                            <td>{{ $service->domain ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @switch($service->status)
                                                    @case('Active')
                                                        <span class="badge bg-success">Active</span>
                                                        @break
                                                    @case('Suspended')
                                                        <span class="badge bg-warning">Suspended</span>
                                                        @break
                                                    @case('Terminated')
                                                        <span class="badge bg-danger">Terminated</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $service->status }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $service->created_at ? $service->created_at->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Next Due:</strong></td>
                                            <td>{{ $service->due_date ? $service->due_date->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Billing Information</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Billing Cycle:</strong></td>
                                            <td>{{ $service->translated_billing_cycle }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Price:</strong></td>
                                            <td>Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Setup Fee:</strong></td>
                                            <td>Rp {{ number_format($service->setup_fee ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Tab -->
                <div class="tab-pane fade" id="actions" role="tabpanel" aria-labelledby="actions-tab">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Available Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Service Management</h6>
                                    <div class="list-group">
                                        <a href="#" class="list-group-item list-group-item-action" onclick="upgradePlan()">
                                            <i class="bx bx-up-arrow-alt me-2"></i>Upgrade Layanan
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action" onclick="changePassword()">
                                            <i class="bx bx-calendar-plus me-2"></i>Perpanjang Layanan
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Support</h6>
                                    <div class="list-group">
                                        <a href="#" class="list-group-item list-group-item-action" onclick="contactSupport()">
                                            <i class="bx bx-message-dots me-2"></i>Contact Support
                                        </a>
                                        <a href="{{ route('client.invoices.index') }}" class="list-group-item list-group-item-action">
                                            <i class="bx bx-receipt me-2"></i>View Invoices
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upgrade Plan Modal -->
<div class="modal fade" id="upgradePlanModal" tabindex="-1" aria-labelledby="upgradePlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="upgradePlanModalLabel">Pricing Plans</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <p class="text-muted">All plans include 40+ advanced tools and features to boost your product. Choose the best plan to fit your needs.</p>
                    
                    <!-- Billing Info -->
                    <div class="d-flex align-items-center justify-content-center mb-4">
                        <span class="text-primary fw-semibold">Annually</span>
                        <span class="badge bg-label-success ms-2">Best Value</span>
                        <input type="hidden" id="billingToggle" value="annually">
                    </div>
                </div>

                <!-- Pricing Cards -->
                <div class="row g-4">
                    @foreach($servicePackages as $index => $package)
                    <div class="col-lg-4">
                        <div class="card border {{ $index === 1 ? 'border-primary' : '' }} h-100 position-relative">
                            @if($index === 1)
                                <div class="position-absolute top-0 start-50 translate-middle">
                                    <span class="badge bg-primary">Popular</span>
                                </div>
                            @endif
                            
                            <div class="card-body text-center p-4">
                                <!-- Plan Icon -->
                                <div class="mb-4">
                                    <div class="avatar avatar-xl mx-auto">
                                        <span class="avatar-initial rounded-circle bg-label-{{ $index === 0 ? 'primary' : ($index === 1 ? 'success' : 'info') }}">
                                            <i class="bx {{ $index === 0 ? 'bx-user' : ($index === 1 ? 'bx-briefcase' : 'bx-crown') }} fs-2"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- Plan Name -->
                                <h4 class="mb-2">{{ $package->name }}</h4>
                                <p class="text-muted mb-4">{{ $package->description }}</p>

                                <!-- Price -->
                                <div class="mb-4">
                                    <h2 class="text-primary mb-0">
                                        Rp {{ number_format($package->base_price, 0, ',', '.') }}
                                    </h2>
                                    <small class="text-muted">/year</small>
                                </div>

                                <!-- Features -->
                                <div class="mb-4">
                                    @if($package->features)
                                        @foreach($package->features as $feature => $value)
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bx bx-check text-success me-2"></i>
                                                <span class="text-muted">
                                                    @if(is_bool($value))
                                                        {{ $value ? ucfirst(str_replace('_', ' ', $feature)) : 'No ' . str_replace('_', ' ', $feature) }}
                                                    @else
                                                        {{ $value }} {{ str_replace('_', ' ', $feature) }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bx bx-check text-success me-2"></i>
                                            <span class="text-muted">Standard features included</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Button -->
                                @if($service->product === $package->name)
                                    <button class="btn btn-success w-100" disabled>
                                        Your Current Plan
                                    </button>
                                @else
                                    <button class="btn {{ $index === 1 ? 'btn-primary' : 'btn-outline-primary' }} w-100" 
                                            onclick="selectPlan({{ $package->id }}, '{{ $package->name }}', {{ $package->base_price }})">
                                        Upgrade
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upgrade Request Modal -->
<div class="modal fade" id="upgradeRequestModal" tabindex="-1" aria-labelledby="upgradeRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="upgradeRequestModalLabel">
                    <i class="bx bx-up-arrow-alt me-2"></i>Request Service Upgrade
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="upgradeRequestForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>Upgrade Request Process:</strong> Your request will be reviewed by our admin team. You will be notified once it's approved and an invoice will be generated.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="current_plan" class="form-label">Current Plan</label>
                                <input type="text" class="form-control" id="current_plan" name="current_plan" 
                                       value="{{ $service->product }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="current_price" class="form-label">Current Price</label>
                                <input type="text" class="form-control" id="current_price" name="current_price" 
                                       value="Rp {{ number_format($service->price, 0, ',', '.') }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="requested_plan" class="form-label">Requested Plan</label>
                                <input type="text" class="form-control" id="requested_plan" name="requested_plan" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="requested_price" class="form-label">New Price</label>
                                <input type="text" class="form-control" id="requested_price" name="requested_price" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="billing_cycle" class="form-label">Billing Cycle</label>
                        <input type="text" class="form-control" id="billing_cycle" name="billing_cycle" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="upgrade_reason" class="form-label">Reason for Upgrade <span class="text-danger">*</span></label>
                        <select class="form-select" id="upgrade_reason" name="upgrade_reason" required>
                            <option value="">Select reason...</option>
                            <option value="need_more_resources">Need More Resources</option>
                            <option value="additional_features">Need Additional Features</option>
                            <option value="business_growth">Business Growth</option>
                            <option value="performance_improvement">Performance Improvement</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="additional_notes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="additional_notes" name="additional_notes" rows="3" 
                                  placeholder="Please provide any additional information about your upgrade request..."></textarea>
                    </div>

                    <!-- Price Comparison -->
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Price Comparison</h6>
                            <div class="row text-center">
                                <div class="col-4">
                                    <small class="text-muted">Current</small>
                                    <div class="fw-bold">Rp {{ number_format($service->price, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-4">
                                    <i class="bx bx-right-arrow-alt text-primary"></i>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">New</small>
                                    <div class="fw-bold text-primary" id="new_price_display">-</div>
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <small class="text-muted">Price difference: </small>
                                <span class="fw-bold" id="price_difference">-</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitUpgradeBtn" onclick="submitUpgradeRequest()">
                        <i class="bx bx-paper-plane me-2"></i>Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility() {
    const passwordField = document.getElementById('password-field');
    const toggleIcon = document.getElementById('password-toggle-icon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('bx-show');
        toggleIcon.classList.add('bx-hide');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('bx-hide');
        toggleIcon.classList.add('bx-show');
    }
}

function copyToClipboard(text) {
    if (text) {
        navigator.clipboard.writeText(text).then(function() {
            showToast('Copied to clipboard!', 'success');
        }).catch(function() {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showToast('Copied to clipboard!', 'success');
        });
    }
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 250px;';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bx bx-check-circle me-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

function loginDashboard(domain) {
    @if($service->login_url)
        window.open('{{ $service->login_url }}', '_blank');
    @elseif($service->domain)
        window.open('https://{{ $service->domain }}/admin', '_blank');
    @else
        alert('No dashboard URL available for this service');
    @endif
}

function contactSupport() {
    // WhatsApp link or support form
    const message = encodeURIComponent('{{ str_replace('{service_name}', $service->product ?? 'Service', config('company.support_messages.service_issue')) }}');
    window.open(`{{ config('company.whatsapp_url') }}?text=${message}`, '_blank');
}

function changePassword() {
    // Confirm renewal
    if (!confirm('Apakah Anda ingin membuat invoice perpanjangan untuk layanan ini?')) {
        return;
    }
    
    // Show loading
    showToast('Membuat invoice perpanjangan...', 'info');
    
    // Create renewal invoice
    fetch(`/services/{{ $service->id }}/renewal`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Invoice perpanjangan berhasil dibuat!', 'success');
            
            // Show confirmation dialog with payment option
            const confirmPayment = confirm(
                `Invoice #${data.invoice_number} telah dibuat.\n\n` +
                `Jumlah: Rp ${Number(data.amount).toLocaleString('id-ID')}\n` +
                `Jatuh Tempo: ${data.due_date}\n\n` +
                `Apakah Anda ingin langsung melakukan pembayaran sekarang?`
            );
            
            if (confirmPayment) {
                window.location.href = data.payment_url;
            } else {
                // Redirect to invoices page after a delay
                setTimeout(() => {
                    window.location.href = '/client/invoices';
                }, 2000);
            }
        } else {
            showToast(data.message || 'Gagal membuat invoice perpanjangan', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat membuat invoice perpanjangan', 'danger');
    });
}

function upgradePlan() {
    // Show upgrade plan modal
    const modal = new bootstrap.Modal(document.getElementById('upgradePlanModal'));
    modal.show();
}

function cancelSubscription() {
    // Show cancellation reason modal
    const reason = prompt('Please provide a reason for cancellation:');
    if (!reason || reason.trim() === '') {
        showToast('Cancellation reason is required.', 'warning');
        return;
    }
    
    // Show loading
    showToast('Processing cancellation request...', 'warning');
    
    // Submit cancellation request
    fetch(`/services/{{ $service->id }}/cancellation-request`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            cancellation_reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Cancellation request submitted successfully! Our admin team will review it shortly.', 'success');
            
            // Optionally refresh the page after a delay
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showToast(data.message || 'Failed to submit cancellation request', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while submitting your request', 'danger');
    });
}

// Billing is now fixed to annually only

function selectPlan(packageId, packageName, price) {
    // Close pricing modal and open upgrade request modal
    const pricingModal = bootstrap.Modal.getInstance(document.getElementById('upgradePlanModal'));
    pricingModal.hide();
    
    // Fill upgrade request form
    document.getElementById('requested_plan').value = packageName;
    document.getElementById('requested_price').value = price;
    
    // Set billing cycle to annually (fixed)
    document.getElementById('billing_cycle').value = 'annually';
    
    // Update price comparison (use base price directly)
    const currentPrice = {{ $service->price }};
    const newPrice = price;
    const priceDifference = newPrice - currentPrice;
    
    document.getElementById('new_price_display').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(newPrice);
    
    const diffElement = document.getElementById('price_difference');
    const sign = priceDifference >= 0 ? '+' : '';
    diffElement.textContent = sign + 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(priceDifference));
    diffElement.className = 'fw-bold ' + (priceDifference >= 0 ? 'text-success' : 'text-danger');
    
    // Show upgrade request modal
    const upgradeRequestModal = new bootstrap.Modal(document.getElementById('upgradeRequestModal'));
    upgradeRequestModal.show();
}

function submitUpgradeRequest() {
    const form = document.getElementById('upgradeRequestForm');
    const formData = new FormData(form);
    
    // Show loading
    const submitBtn = document.getElementById('submitUpgradeBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
    submitBtn.disabled = true;
    
    fetch(`/services/{{ $service->id }}/upgrade-request`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast('Upgrade request submitted successfully!', 'success');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('upgradeRequestModal'));
            modal.hide();
            
            // Reset form
            form.reset();
            
            // Show success message
            setTimeout(() => {
                alert('Your upgrade request has been submitted and is pending admin approval. You will be notified once it\'s processed.');
            }, 1000);
        } else {
            showToast(data.message || 'Failed to submit upgrade request', 'danger');
            console.error('Server error:', data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while submitting your request: ' + error.message, 'danger');
    })
    .finally(() => {
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Auto-refresh upgrade request status every 30 seconds
function refreshUpgradeStatus() {
    fetch(`/services/{{ $service->id }}/upgrade-status`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.hasUpgradeRequest && data.status !== 'pending') {
            // Status changed, reload page to show updated status
            showToast('Your upgrade request status has been updated!', 'info');
            setTimeout(() => location.reload(), 2000);
        }
    })
    .catch(error => {
        console.log('Error checking upgrade status:', error);
    });
}

// Start auto-refresh if there's a pending request
@php
    $hasPendingRequest = \App\Models\ServiceUpgradeRequest::where('service_id', $service->id)
        ->where('client_id', auth()->id())
        ->where('status', 'pending')
        ->exists();
@endphp

@if($hasPendingRequest)
    setInterval(refreshUpgradeStatus, 30000); // Check every 30 seconds
@endif
</script>
@endsection
