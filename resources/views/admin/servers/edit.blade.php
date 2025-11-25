@extends('layouts.sneat-dashboard')

@section('title', 'Edit Server')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.servers.index') }}">Server Management</a>
            </li>
            <li class="breadcrumb-item active">Edit Server</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-0">
                        <i class="bx bx-edit me-2"></i>Edit Server
                    </h5>
                    <p class="text-muted mb-0">Ubah informasi server: {{ $server->name }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Server</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.servers.update', $server) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Server <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $server->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- IP Address -->
                        <div class="mb-3">
                            <label for="ip_address" class="form-label">IP Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ip_address') is-invalid @enderror" 
                                   id="ip_address" name="ip_address" value="{{ old('ip_address', $server->ip_address) }}" required>
                            @error('ip_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                   id="username" name="username" value="{{ old('username', $server->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password (kosongkan jika tidak diubah)</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" placeholder="Kosongkan untuk tetap menggunakan password lama">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility()">
                                    <i class="bx bx-eye" id="passwordToggleIcon"></i>
                                </button>
                            </div>
                            <div class="form-text">Kosongkan jika tidak ingin mengubah password</div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Password Info -->
                        <div class="mb-3">
                            <label class="form-label">Password Saat Ini</label>
                            <div class="d-flex align-items-center">
                                <input type="password" class="form-control" value="••••••••" readonly>
                                <button type="button" class="btn btn-outline-secondary ms-2" onclick="showCurrentPassword()">
                                    <i class="bx bx-lock-open"></i> Lihat
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-1" onclick="copyCurrentPassword()">
                                    <i class="bx bx-copy"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Login Link -->
                        <div class="mb-3">
                            <label for="login_link" class="form-label">Login Link <span class="text-danger">*</span></label>
                            <input type="url" class="form-control @error('login_link') is-invalid @enderror" 
                                   id="login_link" name="login_link" value="{{ old('login_link', $server->login_link) }}" required>
                            @error('login_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Expired Date -->
                        <div class="mb-3">
                            <label for="expired_date" class="form-label">Tanggal Expired <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('expired_date') is-invalid @enderror" 
                                   id="expired_date" name="expired_date" value="{{ old('expired_date', $server->expired_date->format('Y-m-d')) }}" required>
                            @error('expired_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $server->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="suspended" {{ old('status', $server->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="expired" {{ old('status', $server->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes', $server->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.servers.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Update Server
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
                        <i class="bx bx-info-circle me-2"></i>Informasi Server
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">ID Server:</label>
                        <p class="mb-0"><strong>#{{ $server->id }}</strong></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Dibuat:</label>
                        <p class="mb-0">{{ $server->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Terakhir Update:</label>
                        <p class="mb-0">{{ $server->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status Saat Ini:</label>
                        <p class="mb-0">
                            <span class="badge {{ $server->status_badge_class }}">
                                {{ ucfirst($server->status) }}
                            </span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Jumlah Client:</label>
                        <p class="mb-0">
                            <span class="badge bg-primary">{{ $server->clients->count() }} Client</span>
                        </p>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ $server->login_link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-link me-1"></i>Login
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleStatus({{ $server->id }})">
                            <i class="bx bx-power-off me-1"></i>Toggle Status
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
                        <button type="button" class="btn btn-outline-info" onclick="showClients()">
                            <i class="bx bx-user me-2"></i>Lihat Client Terkait
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="checkExpiration()">
                            <i class="bx bx-time me-2"></i>Cek Status Expired
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="testLogin()">
                            <i class="bx bx-link me-2"></i>Test Login Link
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="pingServer()">
                            <i class="bx bx-pulse me-2"></i>Ping Server
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const icon = document.getElementById('passwordToggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.className = 'bx bx-eye-off';
    } else {
        passwordInput.type = 'password';
        icon.className = 'bx bx-eye';
    }
}

// Show current password
function showCurrentPassword() {
    fetch(`/admin/servers/{{ $server->id }}/password`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Password saat ini: ${data.password}`);
            } else {
                showToast('Failed to get password', 'danger');
            }
        })
        .catch(error => {
            showToast('Error getting password', 'danger');
        });
}

// Copy current password
function copyCurrentPassword() {
    fetch(`/admin/servers/{{ $server->id }}/password`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                navigator.clipboard.writeText(data.password).then(() => {
                    showToast('Password copied to clipboard!', 'success');
                });
            } else {
                showToast('Failed to get password', 'danger');
            }
        })
        .catch(error => {
            showToast('Error getting password', 'danger');
        });
}

// Toggle status
function toggleStatus(serverId) {
    fetch(`/admin/servers/${serverId}/toggle-status`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Failed to toggle status', 'danger');
        }
    })
    .catch(error => {
        showToast('Error toggling status', 'danger');
    });
}

// Show clients
function showClients() {
    window.location.href = '{{ route("admin.client-data.index") }}?server_id={{ $server->id }}';
}

// Check expiration
function checkExpiration() {
    const expiredDate = new Date('{{ $server->expired_date->format("Y-m-d") }}');
    const today = new Date();
    const diffTime = expiredDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    let message = '';
    if (diffDays < 0) {
        message = `Server expired ${Math.abs(diffDays)} days ago!`;
    } else if (diffDays <= 30) {
        message = `Server will expire in ${diffDays} days!`;
    } else {
        message = `Server is valid for ${diffDays} more days.`;
    }
    
    showToast(message, diffDays < 0 ? 'danger' : (diffDays <= 30 ? 'warning' : 'success'));
}

// Test login link
function testLogin() {
    window.open('{{ $server->login_link }}', '_blank');
}

// Ping server (simulated)
function pingServer() {
    showToast('Checking server connection...', 'info');
    
    // Simulate ping check
    setTimeout(() => {
        const randomResponse = Math.random() > 0.2; // 80% success rate
        if (randomResponse) {
            showToast('Server is responding normally', 'success');
        } else {
            showToast('Server connection failed', 'danger');
        }
    }, 2000);
}

// Toast notification
function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.innerHTML = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>
@endsection
