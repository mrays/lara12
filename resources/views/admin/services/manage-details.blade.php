@extends('layouts.admin')

@section('title', 'Manage Service Details - ' . ($service->product ?? 'Service'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="tf-icons bx bx-cog me-2"></i>Manage Service Details
                        </h5>
                        <small class="text-muted">Customize how this service appears to the client</small>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">Services</a></li>
                            <li class="breadcrumb-item active">Manage Details</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Info Card -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="tf-icons bx bx-info-circle me-2"></i>Service: {{ $service->product ?? 'N/A' }} 
                        <span class="badge bg-primary ms-2">{{ $service->client_name ?? 'N/A' }}</span>
                    </h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Details Form -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="tf-icons bx bx-edit me-2"></i>Client View Configuration
                    </h6>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6>Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            <i class="tf-icons bx bx-check me-1"></i>{{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.services.update-details', $service->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Service Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="tf-icons bx bx-info-circle me-1"></i>Service Information
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="service_name" class="form-label">Service Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="service_name" name="service_name" 
                                       value="{{ old('service_name', $service->product) }}" required>
                                <small class="text-muted">This will appear as the main service title</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="product" class="form-label">Product <span class="text-danger">*</span></label>
                                <select class="form-select" id="product" name="product" required>
                                    <option value="Starter Hosting" {{ old('product', $service->product) == 'Starter Hosting' ? 'selected' : '' }}>Starter Hosting</option>
                                    <option value="Business Hosting" {{ old('product', $service->product) == 'Business Hosting' ? 'selected' : '' }}>Business Hosting</option>
                                    <option value="Premium Hosting" {{ old('product', $service->product) == 'Premium Hosting' ? 'selected' : '' }}>Premium Hosting</option>
                                    <option value="VPS Hosting" {{ old('product', $service->product) == 'VPS Hosting' ? 'selected' : '' }}>VPS Hosting</option>
                                    <option value="Dedicated Server" {{ old('product', $service->product) == 'Dedicated Server' ? 'selected' : '' }}>Dedicated Server</option>
                                    <option value="Domain Registration" {{ old('product', $service->product) == 'Domain Registration' ? 'selected' : '' }}>Domain Registration</option>
                                    <option value="SSL Certificate" {{ old('product', $service->product) == 'SSL Certificate' ? 'selected' : '' }}>SSL Certificate</option>
                                    <option value="Website Design" {{ old('product', $service->product) == 'Website Design' ? 'selected' : '' }}>Website Design</option>
                                    <option value="SEO Service" {{ old('product', $service->product) == 'SEO Service' ? 'selected' : '' }}>SEO Service</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="domain" class="form-label">Domain</label>
                                <input type="text" class="form-control" id="domain" name="domain" 
                                       value="{{ old('domain', $service->domain) }}" placeholder="example.com">
                                <small class="text-muted">Domain associated with this service</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Active" {{ old('status', $service->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Pending" {{ old('status', $service->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Suspended" {{ old('status', $service->status) == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="Terminated" {{ old('status', $service->status) == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                                    <option value="Dibatalkan" {{ old('status', $service->status) == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                    <option value="Disuspen" {{ old('status', $service->status) == 'Disuspen' ? 'selected' : '' }}>Disuspen</option>
                                    <option value="Sedang Dibuat" {{ old('status', $service->status) == 'Sedang Dibuat' ? 'selected' : '' }}>Sedang Dibuat</option>
                                    <option value="Ditutup" {{ old('status', $service->status) == 'Ditutup' ? 'selected' : '' }}>Ditutup</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="next_due" class="form-label">Next Due Date</label>
                                <input type="date" class="form-control" id="next_due" name="next_due" 
                                       value="{{ old('next_due', $service->due_date ? date('Y-m-d', strtotime($service->due_date)) : '') }}">
                            </div>
                        </div>

                        <!-- Billing Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-success mb-3">
                                    <i class="tf-icons bx bx-credit-card me-1"></i>Billing Information
                                </h6>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="billing_cycle" class="form-label">Billing Cycle <span class="text-danger">*</span></label>
                                <select class="form-select" id="billing_cycle" name="billing_cycle" required>
                                    <option value="Monthly" {{ old('billing_cycle', $service->billing_cycle) == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="Quarterly" {{ old('billing_cycle', $service->billing_cycle) == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
                                    <option value="Semi-Annually" {{ old('billing_cycle', $service->billing_cycle) == 'Semi-Annually' ? 'selected' : '' }}>Semi-Annually</option>
                                    <option value="Annually" {{ old('billing_cycle', $service->billing_cycle) == 'Annually' ? 'selected' : '' }}>Annually</option>
                                    <option value="Biennially" {{ old('billing_cycle', $service->billing_cycle) == 'Biennially' ? 'selected' : '' }}>Biennially</option>
                                    <option value="One Time" {{ old('billing_cycle', $service->billing_cycle) == 'One Time' ? 'selected' : '' }}>One Time</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="price" name="price" 
                                           value="{{ old('price', $service->price) }}" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="setup_fee" class="form-label">Setup Fee</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="setup_fee" name="setup_fee" 
                                           value="{{ old('setup_fee', $service->setup_fee ?? 0) }}" step="0.01" min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Overview/Login Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info mb-3">
                                    <i class="tf-icons bx bx-lock me-1"></i>Overview/Login Information
                                </h6>
                                <p class="text-muted small">This information will be displayed in the client's service overview section</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="{{ old('username', $service->username) }}" placeholder="Enter username or leave N/A">
                                <small class="text-muted">Login username for the service (leave empty for N/A)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" 
                                           value="{{ old('password', $service->password) }}" placeholder="Enter password or leave N/A">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password')">
                                        <i class="tf-icons bx bx-show" id="password-icon"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Login password for the service (leave empty for N/A)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="server" class="form-label">Server</label>
                                <input type="text" class="form-control" id="server" name="server" 
                                       value="{{ old('server', $service->server) }}" placeholder="Server name or IP">
                                <small class="text-muted">Server information (leave empty for "Default Server")</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="login_url" class="form-label">Login Dashboard URL</label>
                                <input type="url" class="form-control" id="login_url" name="login_url" 
                                       value="{{ old('login_url', $service->login_url) }}" placeholder="https://example.com/login">
                                <small class="text-muted">URL for "Login Dashboard" button</small>
                            </div>
                        </div>

                        <!-- Additional Details Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-warning mb-3">
                                    <i class="tf-icons bx bx-note me-1"></i>Additional Details
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="Service description for client">{{ old('description', $service->description) }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="notes" class="form-label">Internal Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="4" 
                                          placeholder="Internal notes (not visible to client)">{{ old('notes', $service->notes) }}</textarea>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                                        <i class="tf-icons bx bx-arrow-back me-1"></i>Back to Services
                                    </a>
                                    <div>
                                        <a href="/client/services/{{ $service->id }}/manage" target="_blank" class="btn btn-info me-2">
                                            <i class="tf-icons bx bx-show me-1"></i>Preview Client View
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="tf-icons bx bx-save me-1"></i>Update Service Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'tf-icons bx bx-hide';
    } else {
        input.type = 'password';
        icon.className = 'tf-icons bx bx-show';
    }
}
</script>
@endsection
