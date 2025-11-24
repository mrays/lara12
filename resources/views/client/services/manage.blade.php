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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $service->product ?? 'Service' }}</h5>
                        <small class="text-muted">{{ $service->domain ?? 'No domain specified' }}</small>
                    </div>
                    <div>
                        @switch($service->status)
                            @case('Active')
                                <span class="badge bg-success">ACTIVE</span>
                                @break
                            @case('Suspended')
                                <span class="badge bg-warning">SUSPENDED</span>
                                @break
                            @case('Terminated')
                                <span class="badge bg-danger">TERMINATED</span>
                                @break
                            @default
                                <span class="badge bg-secondary">{{ strtoupper($service->status) }}</span>
                        @endswitch
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
                            <h5 class="mb-0">{{ $service->product ?? 'Service' }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Username</strong></td>
                                            <td>{{ $service->username ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Password</strong></td>
                                            <td>
                                                <span id="password-hidden">••••••••••••</span>
                                                <span id="password-shown" style="display: none;">{{ $service->password ?? 'N/A' }}</span>
                                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="togglePassword()">
                                                    <i class="bx bx-show" id="password-icon"></i>
                                                </button>
                                                @if($service->password)
                                                <button type="button" class="btn btn-sm btn-outline-primary ms-1" onclick="copyPassword()">
                                                    <i class="bx bx-copy"></i>
                                                </button>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Server</strong></td>
                                            <td>{{ $service->server ?? 'Default Server' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    @if($service->status === 'Active')
                                        <button class="btn btn-primary me-2" onclick="loginDashboard()">
                                            <i class="bx bx-log-in me-1"></i>Login Dashboard
                                        </button>
                                        <button class="btn btn-success" onclick="contactSupport()">
                                            <i class="bx bx-phone me-1"></i>Hubungi Kami
                                        </button>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="bx bx-info-circle me-2"></i>
                                            Service is currently {{ strtolower($service->status) }}. Please contact support for assistance.
                                        </div>
                                        <button class="btn btn-warning" onclick="contactSupport()">
                                            <i class="bx bx-phone me-1"></i>Contact Support
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Tips -->
                            <div class="alert alert-info mt-4">
                                <strong>Tips:</strong>
                                Silakan klik tombol <strong>"Login Dashboard"</strong> untuk masuk ke Dashboard Website
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
                                            <td>{{ $service->created_at->format('M d, Y') }}</td>
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
                                            <td>{{ ucfirst($service->billing_cycle) }}</td>
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
                                        <a href="#" class="list-group-item list-group-item-action" onclick="upgradeService()">
                                            <i class="bx bx-up-arrow-alt me-2"></i>Upgrade Layanan
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action" onclick="changePassword()">
                                            <i class="bx bx-key me-2"></i>Perpanjang Website
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

<script>
function togglePassword() {
    const hidden = document.getElementById('password-hidden');
    const shown = document.getElementById('password-shown');
    const icon = document.getElementById('password-icon');
    
    if (hidden.style.display === 'none') {
        hidden.style.display = 'inline';
        shown.style.display = 'none';
        icon.className = 'bx bx-show';
    } else {
        hidden.style.display = 'none';
        shown.style.display = 'inline';
        icon.className = 'bx bx-hide';
    }
}

function copyPassword() {
    const password = '{{ $service->password ?? '' }}';
    if (password) {
        navigator.clipboard.writeText(password).then(function() {
            alert('Password copied to clipboard!');
        });
    }
}

function loginDashboard() {
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
    const message = encodeURIComponent('Hello, I need support for my service: {{ $service->product ?? "Service" }}');
    window.open(`https://wa.me/6281234567890?text=${message}`, '_blank');
}

function upgradeService() {
    alert('Upgrade service feature coming soon!');
}

function changePassword() {
    alert('Change password feature coming soon!');
}
</script>
@endsection
