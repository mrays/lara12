@extends('layouts.sneat-dashboard')

@section('title', 'Edit Client Data')

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
            <li class="breadcrumb-item active">Edit Client</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-0">
                        <i class="bx bx-edit me-2"></i>Edit Client Data
                    </h5>
                    <p class="text-muted mb-0">Ubah informasi client: {{ $client->name }}</p>
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
                    <form method="POST" action="{{ route('admin.client-data.update', $client) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <h6 class="fw-semibold mb-3">Informasi Dasar</h6>
                        
                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Client <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $client->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3" required>{{ old('address', $client->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- WhatsApp -->
                        <div class="mb-3">
                            <label for="whatsapp" class="form-label">WhatsApp <span class="text-danger">*</span></label>
                            <div class="d-flex align-items-center">
                                <input type="text" class="form-control @error('whatsapp') is-invalid @enderror" 
                                       id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $client->whatsapp) }}" required>
                                <button type="button" class="btn btn-outline-success ms-2" onclick="openWhatsApp()">
                                    <i class="bx bxl-whatsapp"></i>
                                </button>
                            </div>
                            @error('whatsapp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Other Settings -->
                        <h6 class="fw-semibold mb-3 mt-4">Pengaturan Lainnya</h6>

                        <!-- User -->
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Link ke User (Opsional)</label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                                <option value="">Pilih User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $client->user_id) == $user->id ? 'selected' : '' }}>
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
                                <option value="active" {{ old('status', $client->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="warning" {{ old('status', $client->status) == 'warning' ? 'selected' : '' }}>Warning</option>
                                <option value="expired" {{ old('status', $client->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes', $client->notes) }}</textarea>
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
                                <i class="bx bx-save me-1"></i>Update Client
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-info-circle me-2"></i>Informasi Client
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">ID Client:</label>
                        <p class="mb-0"><strong>#{{ $client->id }}</strong></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Dibuat:</label>
                        <p class="mb-0">{{ $client->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Terakhir Update:</label>
                        <p class="mb-0">{{ $client->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status Saat Ini:</label>
                        <p class="mb-0">
                            <span class="badge {{ $client->status_badge_class }}">
                                {{ ucfirst($client->status) }}
                            </span>
                        </p>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ $client->whatsapp_link }}" target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="bx bxl-whatsapp me-1"></i>WhatsApp
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="sendEmail()">
                            <i class="bx bx-envelope me-1"></i>Email
                        </button>
                    </div>
                </div>
            </div>

            <!-- Domain List -->
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bx bx-globe me-2"></i>Domain Client
                    </h5>
                    <a href="{{ route('admin.domains.create') }}?client_id={{ $client->id }}" class="btn btn-sm btn-primary">
                        <i class="bx bx-plus"></i>
                    </a>
                </div>
                <div class="card-body">
                    @if($clientDomains->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($clientDomains as $domain)
                                <div class="list-group-item px-0 d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $domain->domain_name }}</strong>
                                        @if($domain->expired_date)
                                            <br>
                                            <small class="text-muted">
                                                Exp: 
                                                <span class="{{ $domain->expired_date->isPast() ? 'text-danger' : ($domain->expired_date->lte(now()->addDays(30)) ? 'text-warning' : 'text-success') }}">
                                                    {{ $domain->expired_date->format('M d, Y') }}
                                                </span>
                                            </small>
                                        @endif
                                        @if($domain->server)
                                            <br><small class="text-muted"><i class="bx bx-server"></i> {{ $domain->server->name }}</small>
                                        @endif
                                    </div>
                                    <a href="{{ route('admin.domains.edit', $domain->id) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="bx bx-folder-open fs-1"></i>
                            <p class="mb-0">Belum ada domain</p>
                            <a href="{{ route('admin.domains.create') }}?client_id={{ $client->id }}" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="bx bx-plus me-1"></i>Tambah Domain
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.domains.create') }}?client_id={{ $client->id }}" class="btn btn-outline-primary">
                            <i class="bx bx-plus me-2"></i>Tambah Domain
                        </a>
                        <button type="button" class="btn btn-outline-success" onclick="sendRenewalReminder()">
                            <i class="bx bx-bell me-2"></i>Kirim Reminder
                        </button>
                        @if($client->user)
                        <button type="button" class="btn btn-outline-secondary" onclick="viewUser()">
                            <i class="bx bx-user me-2"></i>Lihat User
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Open WhatsApp
function openWhatsApp() {
    const whatsapp = document.getElementById('whatsapp').value;
    const cleanNumber = whatsapp.replace(/[^0-9+]/g, '');
    window.open(`https://wa.me/${cleanNumber}`, '_blank');
}

// Send email
function sendEmail() {
    const subject = encodeURIComponent('Informasi Layanan - {{ $client->name }}');
    const body = encodeURIComponent('Halo {{ $client->name }},\n\nTerima kasih telah menggunakan layanan kami.\n\nSalam.');
    window.open(`mailto:?subject=${subject}&body=${body}`);
}

// Send renewal reminder
function sendRenewalReminder() {
    @if($clientDomains->count() > 0)
        let domainList = '';
        @foreach($clientDomains as $domain)
            @if($domain->expired_date)
                domainList += '- {{ $domain->domain_name }}: {{ $domain->expired_date->format("M d, Y") }}\n';
            @endif
        @endforeach
        const message = encodeURIComponent(`Halo {{ $client->name }},\n\nIni adalah pengingat untuk layanan domain Anda:\n\n${domainList}\nSilakan lakukan perpanjangan sebelum tanggal kadaluarsa.\n\nTerima kasih.`);
        window.open(`{{ $client->whatsapp_link }}?text=${message}`, '_blank');
    @else
        showToast('Client belum memiliki domain', 'warning');
    @endif
}

// View user
function viewUser() {
    @if($client->user)
    window.open(`/admin/users/{{ $client->user->id }}/edit`, '_blank');
    @else
    showToast('Client tidak memiliki user terkait', 'warning');
    @endif
}

// Toast notification
function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.style.whiteSpace = 'pre-line';
    toast.innerHTML = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 5000);
}
</script>
@endsection
