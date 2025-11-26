@extends('layouts.sneat-dashboard')

@section('title', 'Tambah Client Data')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.client-data.index') }}">Client Data</a>
            </li>
            <li class="breadcrumb-item active">Tambah Client</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-0">
                        <i class="bx bx-plus me-2"></i>Tambah Client Data Baru
                    </h5>
                    <p class="text-muted mb-0">Tambahkan data client baru ke sistem</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Client</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.client-data.store') }}">
                        @csrf
                        
                        <!-- Basic Information -->
                        <h6 class="fw-semibold mb-3">Informasi Dasar</h6>
                        
                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Client <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- WhatsApp -->
                        <div class="mb-3">
                            <label for="whatsapp" class="form-label">WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('whatsapp') is-invalid @enderror" 
                                   id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}" required
                                   placeholder="+62812345678">
                            @error('whatsapp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Domain Selection -->
                        <h6 class="fw-semibold mb-3 mt-4">Informasi Layanan</h6>

                        <!-- Domain -->
                        <div class="mb-3">
                            <label for="domain_id" class="form-label">Domain <span class="text-danger">*</span></label>
                            <select class="form-select @error('domain_id') is-invalid @enderror" id="domain_id" name="domain_id" required>
                                <option value="">Pilih Domain</option>
                                @foreach($domains as $domain)
                                    <option value="{{ $domain->id }}" {{ old('domain_id') == $domain->id ? 'selected' : '' }}>
                                        {{ $domain->domain_name }}
                                        @if($domain->expired_date)
                                            (Expired: {{ $domain->expired_date->format('M d, Y') }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('domain_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Tanggal expired layanan akan mengikuti expired date domain yang dipilih</div>
                        </div>

                        <!-- Assignments -->
                        <h6 class="fw-semibold mb-3 mt-4">Penugasan</h6>

                        <!-- Server -->
                        <div class="mb-3">
                            <label for="server_id" class="form-label">Server Hosting</label>
                            <select class="form-select @error('server_id') is-invalid @enderror" id="server_id" name="server_id">
                                <option value="">Pilih Server</option>
                                @foreach($servers as $server)
                                    <option value="{{ $server->id }}" {{ old('server_id') == $server->id ? 'selected' : '' }}>
                                        {{ $server->name }} ({{ $server->ip_address }})
                                    </option>
                                @endforeach
                            </select>
                            @error('server_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Domain Register -->
                        <div class="mb-3">
                            <label for="domain_register_id" class="form-label">Domain Register</label>
                            <select class="form-select @error('domain_register_id') is-invalid @enderror" id="domain_register_id" name="domain_register_id">
                                <option value="">Pilih Register</option>
                                @foreach($domainRegisters as $register)
                                    <option value="{{ $register->id }}" {{ old('domain_register_id') == $register->id ? 'selected' : '' }}>
                                        {{ $register->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('domain_register_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- User -->
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Link ke User (Opsional)</label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                                <option value="">Pilih User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="warning" {{ old('status') == 'warning' ? 'selected' : '' }}>Warning</option>
                                <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.client-data.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Simpan Client
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-help-circle me-2"></i>Bantuan
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-semibold">Informasi Client:</h6>
                    <ul class="mb-3">
                        <li><strong>Nama Client:</strong> Nama lengkap perusahaan/individu</li>
                        <li><strong>Alamat:</strong> Alamat lengkap client</li>
                        <li><strong>WhatsApp:</strong> Nomor WhatsApp dengan kode negara (+62)</li>
                    </ul>

                    <h6 class="fw-semibold">Tanggal Expired:</h6>
                    <ul class="mb-3">
                        <li><strong>Domain:</strong> Semua layanan mengikuti tanggal expired domain yang dipilih</li>
                        <li><strong>Website Service:</strong> Mengikuti expired domain</li>
                        <li><strong>Hosting:</strong> Mengikuti expired domain</li>
                    </ul>

                    <h6 class="fw-semibold">Penugasan:</h6>
                    <ul class="mb-3">
                        <li><strong>Domain:</strong> Domain yang digunakan client (menentukan expired layanan)</li>
                        <li><strong>Server:</strong> Server hosting yang digunakan</li>
                        <li><strong>Domain Register:</strong> Tempat registrasi domain</li>
                        <li><strong>User:</strong> Link ke user di sistem (opsional)</li>
                    </ul>

                    <h6 class="fw-semibold">Status:</h6>
                    <ul class="mb-3">
                        <li><span class="badge bg-success">Active</span> - Semua layanan aktif</li>
                        <li><span class="badge bg-warning">Warning</span> - Ada layanan hampir expired</li>
                        <li><span class="badge bg-danger">Expired</span> - Ada layanan expired</li>
                    </ul>

                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>
                        <small>Status akan otomatis diupdate berdasarkan tanggal expired.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date for domain selection if needed
    const domainSelect = document.getElementById('domain_id');
    if (domainSelect) {
        domainSelect.focus();
    }
});
</script>
@endsection
