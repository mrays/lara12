@extends('layouts.sneat-dashboard')

@section('title', 'Add Domain')

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
            <li class="breadcrumb-item active">Add Domain</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-plus me-2"></i>Add New Domain
                    </h5>
                    <small class="text-muted">Create a new domain with client and server assignments</small>
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
                    <form method="POST" action="{{ route('admin.domains.store') }}">
                        @csrf
                        
                        <!-- Domain Name -->
                        <div class="mb-3">
                            <label for="domain_name" class="form-label">Domain Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-link"></i>
                                </span>
                                <input type="text" class="form-control @error('domain_name') is-invalid @enderror" 
                                       id="domain_name" name="domain_name" 
                                       value="{{ old('domain_name') }}" 
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
                                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
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
                                        <option value="{{ $server->id }}" {{ old('server_id') == $server->id ? 'selected' : '' }}>
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
                                                {{ old('domain_register_id') == $register->id ? 'selected' : '' }}>
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
                                       value="{{ old('expired_date') }}">
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
                                        <option value="{{ $value }}" {{ old('status', 'active') == $value ? 'selected' : '' }}>
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
                                          placeholder="Additional notes about this domain...">{{ old('notes') }}</textarea>
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
                                    <i class="bx bx-save me-1"></i>Create Domain
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Guide -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-info-circle me-2"></i>Quick Guide
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">
                            <i class="bx bx-link me-1"></i>Domain Name
                        </h6>
                        <p class="text-muted small mb-2">
                            Enter the domain name without protocol or subdomain:
                        </p>
                        <ul class="text-muted small mb-0">
                            <li>✅ example.com</li>
                            <li>✅ my-site.org</li>
                            <li>❌ https://example.com</li>
                            <li>❌ www.example.com</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-primary mb-2">
                            <i class="bx bx-user me-1"></i>Client Assignment
                        </h6>
                        <p class="text-muted small mb-0">
                            Assign domain to a client for better organization and tracking. This helps in client-specific reporting and management.
                        </p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-primary mb-2">
                            <i class="bx bx-server me-1"></i>Server Assignment
                        </h6>
                        <p class="text-muted small mb-0">
                            Assign domain to a server to track hosting relationships and server load distribution.
                        </p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-primary mb-2">
                            <i class="bx bx-calendar me-1"></i>Expiration Date
                        </h6>
                        <p class="text-muted small mb-0">
                            Set manual expiration date for renewal tracking. The system will send automatic reminders.
                        </p>
                    </div>

                    <div class="mb-0">
                        <h6 class="text-primary mb-2">
                            <i class="bx bx-flag me-1"></i>Status Options
                        </h6>
                        <ul class="text-muted small mb-0">
                            <li><strong>Active:</strong> Domain is active and working</li>
                            <li><strong>Expired:</strong> Domain has expired</li>
                            <li><strong>Pending:</strong> Domain setup in progress</li>
                            <li><strong>Suspended:</strong> Domain temporarily suspended</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-chart me-2"></i>Current Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Domains:</span>
                        <span class="badge bg-primary">{{ \Illuminate\Support\Facades\DB::select('SELECT COUNT(*) as count FROM domains')[0]->count }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Active:</span>
                        <span class="badge bg-success">{{ \Illuminate\Support\Facades\DB::select('SELECT COUNT(*) as count FROM domains WHERE status = ?', ['active'])[0]->count }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Expired:</span>
                        <span class="badge bg-danger">{{ \Illuminate\Support\Facades\DB::select('SELECT COUNT(*) as count FROM domains WHERE expired_date < ?', [now()])[0]->count }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Expiring Soon:</span>
                        <span class="badge bg-warning">{{ \Illuminate\Support\Facades\DB::select('SELECT COUNT(*) as count FROM domains WHERE expired_date >= ? AND expired_date <= ?', [now(), now()->addDays(30)])[0]->count }}</span>
                    </div>
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
    
    if (expiredDate && !document.getElementById('expired_date').value) {
        document.getElementById('expired_date').value = expiredDate;
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
</script>
