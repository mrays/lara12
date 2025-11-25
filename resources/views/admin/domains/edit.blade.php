@extends('layouts.sneat-dashboard')

@section('title', 'Edit Domain')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.domains.index') }}">Domains</a>
            </li>
            <li class="breadcrumb-item active">Edit Domain</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-edit me-2"></i>Edit Domain: {{ $domain->domain_name }}
                    </h5>
                    <small class="text-muted">Update domain information and assignments</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Domain Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.domains.update', $domain->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Domain Name -->
                        <div class="mb-3">
                            <label for="domain_name" class="form-label">Domain Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-link"></i>
                                </span>
                                <input type="text" class="form-control @error('domain_name') is-invalid @enderror" 
                                       id="domain_name" name="domain_name" 
                                       value="{{ old('domain_name', $domain->domain_name) }}" 
                                       placeholder="example.com"
                                       required>
                            </div>
                            @error('domain_name')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="text-muted">Enter the domain name without www or http://</small>
                        </div>

                        <!-- Client Assignment -->
                        <div class="mb-3">
                            <label for="client_id" class="form-label">Assign to Client</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-user"></i>
                                </span>
                                <select class="form-select @error('client_id') is-invalid @enderror" 
                                        id="client_id" name="client_id">
                                    <option value="">Select Client (Optional)</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" 
                                                {{ old('client_id', $domain->client_id) == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('client_id')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="text-muted">Assign this domain to a client (optional)</small>
                        </div>

                        <!-- Server Assignment -->
                        <div class="mb-3">
                            <label for="server_id" class="form-label">Assign to Server</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-server"></i>
                                </span>
                                <select class="form-select @error('server_id') is-invalid @enderror" 
                                        id="server_id" name="server_id">
                                    <option value="">Select Server (Optional)</option>
                                    @foreach($servers as $server)
                                        <option value="{{ $server->id }}" 
                                                {{ old('server_id', $domain->server_id) == $server->id ? 'selected' : '' }}>
                                            {{ $server->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('server_id')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="text-muted">Assign this domain to a server (optional)</small>
                        </div>

                        <!-- Domain Register Assignment -->
                        <div class="mb-3">
                            <label for="domain_register_id" class="form-label">Domain Register</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-globe"></i>
                                </span>
                                <select class="form-select @error('domain_register_id') is-invalid @enderror" 
                                        id="domain_register_id" name="domain_register_id">
                                    <option value="">Select Domain Register (Optional)</option>
                                    @foreach($domainRegisters as $register)
                                        <option value="{{ $register->id }}" 
                                                data-expired="{{ $register->expired_date->format('Y-m-d') }}"
                                                {{ old('domain_register_id', $domain->domain_register_id) == $register->id ? 'selected' : '' }}>
                                            {{ $register->name }} ({{ $register->expired_date->format('M d, Y') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('domain_register_id')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="text-muted">Select the domain register where this domain is managed</small>
                        </div>

                        <!-- Expiration Date -->
                        <div class="mb-3">
                            <label for="expired_date" class="form-label">Expiration Date</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-calendar"></i>
                                </span>
                                <input type="date" class="form-control @error('expired_date') is-invalid @enderror" 
                                       id="expired_date" name="expired_date" 
                                       value="{{ old('expired_date', $domain->expired_date ? $domain->expired_date->format('Y-m-d') : '') }}">
                            </div>
                            @error('expired_date')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="text-muted">Set when this domain expires (optional)</small>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-flag"></i>
                                </span>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', $domain->status) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('status')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="text-muted">Current status of this domain</small>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">Notes</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-comment"></i>
                                </span>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" 
                                          placeholder="Additional notes about this domain...">{{ old('notes', $domain->notes) }}</textarea>
                            </div>
                            @error('notes')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="text-muted">Any additional information about this domain</small>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.domains.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-left me-1"></i>Back to Domains
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-2">
                                    <i class="bx bx-refresh me-1"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i>Update Domain
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Domain Info & Actions -->
        <div class="col-xl-4">
            <!-- Current Domain Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-info-circle me-2"></i>Current Domain Info
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">
                            <i class="bx bx-link me-1"></i>Domain
                        </h6>
                        <p class="mb-0"><strong>{{ $domain->domain_name }}</strong></p>
                    </div>

                    @if($domain->client_name)
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">
                            <i class="bx bx-user me-1"></i>Client
                        </h6>
                        <p class="mb-0">{{ $domain->client_name }}</p>
                    </div>
                    @endif

                    @if($domain->server_name)
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">
                            <i class="bx bx-server me-1"></i>Server
                        </h6>
                        <p class="mb-0">{{ $domain->server_name }}</p>
                    </div>
                    @endif

                    @if($domain->domain_register_name)
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">
                            <i class="bx bx-globe me-1"></i>Domain Register
                        </h6>
                        <p class="mb-0">{{ $domain->domain_register_name }}</p>
                    </div>
                    @endif

                    <div class="mb-3">
                        <h6 class="text-primary mb-2">
                            <i class="bx bx-calendar me-1"></i>Expiration
                        </h6>
                        <p class="mb-0">
                            @if($domain->expired_date)
                                <span class="badge bg-{{ $domain->expired_date->isPast() ? 'danger' : ($domain->expired_date->lte(now()->addDays(30)) ? 'bg-warning' : 'bg-success') }}">
                                    {{ $domain->expired_date->format('M d, Y') }}
                                </span>
                                @php
                                    $daysLeft = $domain->expired_date->diffInDays(now(), false);
                                    $isExpired = $domain->expired_date->isPast();
                                    $daysRounded = abs(round($daysLeft));
                                @endphp
                                <small class="text-muted">
                                    (@if($isExpired)-{{ $daysRounded }} hari@else{{ $daysRounded }} hari lagi@endif)
                                </small>
                            @else
                                <span class="badge bg-secondary">Not Set</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-0">
                        <h6 class="text-primary mb-2">
                            <i class="bx bx-flag me-1"></i>Status
                        </h6>
                        <p class="mb-0">
                            <span class="badge bg-{{ $domain->status == 'active' ? 'success' : ($domain->status == 'expired' ? 'danger' : ($domain->status == 'pending' ? 'warning' : 'secondary')) }}">
                                {{ ucfirst($domain->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-run me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($domain->expired_date)
                        <form method="POST" action="{{ route('admin.domains.send-reminder', $domain->id) }}" target="_blank">
                            @csrf
                            <button type="submit" class="btn btn-outline-warning">
                                <i class="bx bx-bell me-1"></i>Send Renewal Reminder
                            </button>
                        </form>
                        @endif
                        
                        @if($domain->domain_register_name)
                        <a href="#" class="btn btn-outline-info" onclick="alert('Feature coming soon: Open register dashboard')">
                            <i class="bx bx-link me-1"></i>Open Register Dashboard
                        </a>
                        @endif
                        
                        @if($domain->client_name)
                        <a href="{{ route('admin.client-data.edit', $domain->client_id) }}" class="btn btn-outline-success">
                            <i class="bx bx-user me-1"></i>View Client Details
                        </a>
                        @endif
                        
                        @if($domain->server_name)
                        <a href="{{ route('admin.servers.edit', $domain->server_id) }}" class="btn btn-outline-secondary">
                            <i class="bx bx-server me-1"></i>View Server Details
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-error me-2"></i>Danger Zone
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Once you delete a domain, there is no going back. Please be certain.
                    </p>
                    <form method="POST" action="{{ route('admin.domains.destroy', $domain->id) }}" onsubmit="return confirm('Are you sure you want to delete this domain? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bx bx-trash me-1"></i>Delete Domain
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
// Auto-sync expiration date with domain register
document.getElementById('domain_register_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const expiredDate = selectedOption.getAttribute('data-expired');
    const currentExpiredDate = document.getElementById('expired_date').value;
    
    // Only auto-fill if current date is empty
    if (expiredDate && !currentExpiredDate) {
        if (confirm('Would you like to set the expiration date to match the domain register expiration date?')) {
            document.getElementById('expired_date').value = expiredDate;
        }
    }
});

// Validate domain name format
document.getElementById('domain_name').addEventListener('blur', function() {
    const value = this.value.trim();
    
    // Remove protocol if present
    if (value.startsWith('http://') || value.startsWith('https://')) {
        this.value = value.replace(/^https?:\/\//, '');
    }
    
    // Remove www if present
    if (value.startsWith('www.')) {
        this.value = value.replace(/^www\./, '');
    }
    
    // Basic domain validation
    const domainRegex = /^[a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9](?:\.[a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9])*$/;
    if (value && !domainRegex.test(value)) {
        this.classList.add('is-invalid');
        
        // Show error message
        let errorDiv = this.parentNode.nextElementSibling;
        if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback d-block';
            this.parentNode.parentNode.insertBefore(errorDiv, this.parentNode.nextSibling);
        }
        errorDiv.textContent = 'Please enter a valid domain name (e.g., example.com)';
    } else {
        this.classList.remove('is-invalid');
        const errorDiv = this.parentNode.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.remove();
        }
    }
});

// Status change confirmation
document.getElementById('status').addEventListener('change', function() {
    const newStatus = this.value;
    const currentStatus = '{{ $domain->status }}';
    
    if (newStatus === 'expired' && currentStatus !== 'expired') {
        if (!confirm('Are you sure you want to mark this domain as expired? This will affect expiration tracking.')) {
            this.value = currentStatus;
        }
    }
    
    if (newStatus === 'suspended' && currentStatus !== 'suspended') {
        if (!confirm('Are you sure you want to suspend this domain? This may affect client services.')) {
            this.value = currentStatus;
        }
    }
});
</script>
