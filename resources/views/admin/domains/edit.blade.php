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
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Enter the domain name without www or http://</small>
                        </div>

                        <!-- Client Assignment -->
                        <div class="mb-3">
                            <label for="client_id" class="form-label">Assign to Client</label>
                            <select class="form-select @error('client_id') is-invalid @enderror" id="client_id" name="client_id">
                                <option value="">Select Client (Optional)</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', $domain->client_id) == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Server Assignment -->
                        <div class="mb-3">
                            <label for="server_id" class="form-label">Assign to Server</label>
                            <select class="form-select @error('server_id') is-invalid @enderror" id="server_id" name="server_id">
                                <option value="">Select Server (Optional)</option>
                                @foreach($servers as $server)
                                    <option value="{{ $server->id }}" {{ old('server_id', $domain->server_id) == $server->id ? 'selected' : '' }}>
                                        {{ $server->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('server_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Domain Register Assignment -->
                        <div class="mb-3">
                            <label for="domain_register_id" class="form-label">Domain Register</label>
                            <select class="form-select @error('domain_register_id') is-invalid @enderror" id="domain_register_id" name="domain_register_id">
                                <option value="">Select Domain Register (Optional)</option>
                                @foreach($domainRegisters as $register)
                                    <option value="{{ $register->id }}" 
                                            data-expired="{{ $register->expired_date->format('Y-m-d') }}"
                                            {{ old('domain_register_id', $domain->domain_register_id) == $register->id ? 'selected' : '' }}>
                                        {{ $register->name }} ({{ $register->expired_date->format('M d, Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('domain_register_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Expiration Date -->
                        <div class="mb-3">
                            <label for="expired_date" class="form-label">Expiration Date</label>
                            <input type="date" class="form-control @error('expired_date') is-invalid @enderror" 
                                   id="expired_date" name="expired_date" 
                                   value="{{ old('expired_date', $domain->expired_date ? $domain->expired_date->format('Y-m-d') : '') }}">
                            @error('expired_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', $domain->status) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Additional notes about this domain...">{{ old('notes', $domain->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.domains.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-left me-1"></i>Back to Domains
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Update Domain
                            </button>
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
                    <h6 class="mb-0"><i class="bx bx-info-circle me-2"></i>Current Domain Info</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Domain:</strong> {{ $domain->domain_name }}
                    </div>
                    @if($domain->client_name)
                        <div class="mb-3">
                            <strong>Client:</strong> {{ $domain->client_name }}
                        </div>
                    @endif
                    @if($domain->server_name)
                        <div class="mb-3">
                            <strong>Server:</strong> {{ $domain->server_name }}
                        </div>
                    @endif
                    @if($domain->expired_date)
                        <div class="mb-3">
                            <strong>Expiration:</strong>
                            <span class="badge bg-{{ $domain->expired_date->isPast() ? 'danger' : 'success' }}">
                                {{ $domain->expired_date->format('M d, Y') }}
                            </span>
                        </div>
                    @endif
                    <div class="mb-0">
                        <strong>Status:</strong>
                        <span class="badge bg-{{ $domain->status == 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($domain->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bx bx-error me-2"></i>Danger Zone</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Once you delete a domain, there is no going back.
                    </p>
                    <form method="POST" action="{{ route('admin.domains.destroy', $domain->id) }}" onsubmit="return confirm('Are you sure?')">
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
