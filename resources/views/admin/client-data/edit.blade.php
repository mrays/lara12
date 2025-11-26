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

                        <!-- Domain Selection -->
                        <h6 class="fw-semibold mb-3 mt-4">Informasi Layanan</h6>

                        <!-- Domain -->
                        <div class="mb-3">
                            <label for="domain_id" class="form-label">Domain <span class="text-danger">*</span></label>
                            <select class="form-select @error('domain_id') is-invalid @enderror" id="domain_id" name="domain_id" required>
                                <option value="">Pilih Domain</option>
                                @foreach($domains as $domain)
                                    <option value="{{ $domain->id }}" {{ old('domain_id', $client->domain_id) == $domain->id ? 'selected' : '' }}>
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
                                    <option value="{{ $server->id }}" {{ old('server_id', $client->server_id) == $server->id ? 'selected' : '' }}>
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
                            <label for="domain_register_id" class="form-label">Domain Register <small class="text-muted">(Opsional)</small></label>
                            <select class="form-select @error('domain_register_id') is-invalid @enderror" id="domain_register_id" name="domain_register_id" onchange="syncDatesFromRegister()">
                                <option value="">-- Pilih Domain Register --</option>
                                @if($domainRegisters->count() > 0)
                                    @foreach($domainRegisters as $register)
                                        <option value="{{ $register->id }}" 
                                                data-domain-expired="{{ $register->expired_date ? $register->expired_date->format('Y-m-d') : '' }}"
                                                {{ old('domain_register_id', $client->domain_register_id) == $register->id ? 'selected' : '' }}>
                                            {{ $register->name }} 
                                            @if($register->expired_date)
                                                (Exp: {{ $register->expired_date->format('M d, Y') }})
                                            @endif
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Tidak ada domain register tersedia</option>
                                @endif
                            </select>
                            @if($domainRegisters->count() == 0)
                                <small class="text-warning">
                                    <i class="bx bx-info-circle"></i> 
                                    <a href="{{ route('admin.domain-registers.create') }}" target="_blank">Tambah Domain Register</a> terlebih dahulu
                                </small>
                            @endif
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

                    <!-- Expiration Status -->
                    <div class="mb-3">
                        <label class="form-label">Status Expiration:</label>
                        @if($client->domain && $client->domain->expired_date)
                            <div class="d-flex flex-column gap-1">
                                <div>
                                    <small>Domain:</small>
                                    <span class="badge {{ $client->domain->expired_date->isPast() ? 'bg-danger' : ($client->domain->expired_date->lte(now()->addDays(30)) ? 'bg-warning' : 'bg-success') }} ms-2">
                                        {{ $client->domain->expired_date->format('M d, Y') }}
                                    </span>
                                </div>
                                <div class="text-muted">
                                    <small>({{ $client->domain->domain_name }})</small>
                                </div>
                                <div class="text-muted mt-1">
                                    <small><i class="bx bx-info-circle"></i> All services follow domain expiration</small>
                                </div>
                            </div>
                        @else
                            <div class="text-muted">
                                <small>No domain assigned</small>
                            </div>
                        @endif
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

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($client->server)
                        <button type="button" class="btn btn-outline-primary" onclick="viewServer()">
                            <i class="bx bx-server me-2"></i>Lihat Server
                        </button>
                        @endif
                        @if($client->domainRegister)
                        <button type="button" class="btn btn-outline-info" onclick="viewRegister()">
                            <i class="bx bx-globe me-2"></i>Lihat Register
                        </button>
                        @endif
                        <button type="button" class="btn btn-outline-warning" onclick="checkAllExpirations()">
                            <i class="bx bx-time me-2"></i>Cek Semua Expired
                        </button>
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
    @if($client->domain && $client->domain->expired_date)
        const subject = encodeURIComponent('Informasi Layanan - {{ $client->name }}');
        const body = encodeURIComponent('Halo {{ $client->name }},\n\nBerikut informasi layanan Anda:\n\n- Domain ({{ $client->domain->domain_name }}): {{ $client->domain->expired_date->format('M d, Y') }}\n- Website Service: Mengikuti expired domain\n- Hosting: Mengikuti expired domain\n\nTerima kasih.');
        window.open(`mailto:?subject=${subject}&body=${body}`);
    @else
        showToast('No domain assigned to this client', 'warning');
    @endif
}

// View server
function viewServer() {
    @if($client->server)
    window.open('{{ route("admin.servers.edit", $client->server) }}', '_blank');
    @else
    showToast('Client tidak memiliki server', 'warning');
    @endif
}

// View register
function viewRegister() {
    @if($client->domainRegister)
    window.open('{{ route("admin.domain-registers.edit", $client->domainRegister) }}', '_blank');
    @else
    showToast('Client tidak memiliki domain register', 'warning');
    @endif
}

// Check all expirations
function checkAllExpirations() {
    @if($client->domain && $client->domain->expired_date)
        const domainDate = new Date('{{ $client->domain->expired_date->format("Y-m-d") }}');
        const today = new Date();
        const diffTime = domainDate - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        let message = '';
        if (diffDays < 0) {
            message = `Domain ({{ $client->domain->domain_name }}): Expired ${Math.abs(diffDays)} days ago`;
        } else if (diffDays <= 30) {
            message = `Domain ({{ $client->domain->domain_name }}): Will expire in ${diffDays} days`;
        } else {
            message = `Domain ({{ $client->domain->domain_name }}): Valid for ${diffDays} more days`;
        }
        
        showToast(message + '\nAll services follow domain expiration', 'info');
    @else
        showToast('No domain assigned to this client', 'warning');
    @endif
}

// Send renewal reminder
function sendRenewalReminder() {
    @if($client->domain && $client->domain->expired_date)
        const message = encodeURIComponent(`Halo {{ $client->name }},\n\nIni adalah pengingat untuk layanan Anda:\n\n- Domain ({{ $client->domain->domain_name }}): {{ $client->domain->expired_date->format('M d, Y') }}\n- Website Service: Mengikuti expired domain\n- Hosting: Mengikuti expired domain\n\nSilakan lakukan perpanjangan sebelum tanggal kadaluarsa.\n\nTerima kasih.`);
        window.open(`{{ $client->whatsapp_link }}?text=${message}`, '_blank');
    @else
        showToast('No domain assigned to this client', 'warning');
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
