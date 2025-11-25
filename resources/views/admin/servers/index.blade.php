@extends('layouts.sneat-dashboard')

@section('title', 'Server Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Server Management</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="bx bx-server me-2"></i>Server Management
                            </h5>
                            <p class="text-muted mb-0">Kelola server hosting dan informasi login</p>
                        </div>
                        <a href="{{ route('admin.servers.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Tambah Server
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success bg-lighten">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-label-success me-3">
                            <i class="bx bx-check-circle fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $statusCounts['active'] }}</h4>
                            <small class="text-muted">Active</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger bg-lighten">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-label-danger me-3">
                            <i class="bx bx-x-circle fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $statusCounts['expired'] }}</h4>
                            <small class="text-muted">Expired</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning bg-lighten">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-label-warning me-3">
                            <i class="bx bx-pause-circle fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $statusCounts['suspended'] }}</h4>
                            <small class="text-muted">Suspended</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary bg-lighten">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-label-primary me-3">
                            <i class="bx bx-server fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $statusCounts['all'] }}</h4>
                            <small class="text-muted">Total Server</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.servers.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Cari nama server, IP..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('admin.servers.index') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Servers List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Daftar Server</h5>
        </div>
        <div class="card-body">
            @if($servers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Server</th>
                                <th>IP Address</th>
                                <th>Username</th>
                                <th>Expired Date</th>
                                <th>Status</th>
                                <th>Clients</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($servers as $server)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $server->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $server->login_link }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <code>{{ $server->ip_address }}</code>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span>{{ $server->username }}</span>
                                            <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $server->username }}')" title="Copy">
                                                <i class="bx bx-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="{{ $server->isExpired() ? 'text-danger' : ($server->isExpiringSoon() ? 'text-warning' : 'text-success') }}">
                                                {{ $server->expired_date->format('M d, Y') }}
                                            </span>
                                            @if($server->isExpired())
                                                <span class="badge bg-danger ms-2">Expired</span>
                                            @elseif($server->isExpiringSoon())
                                                <span class="badge bg-warning ms-2">Expiring Soon</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $server->status_badge_class }}">
                                            {{ ucfirst($server->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-primary">{{ $server->clients->count() }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" type="button" onclick="openActionModal({{ $server->id }}, '{{ $server->name }}', '{{ $server->login_link }}', '{{ route('admin.servers.edit', $server) }}')">
                                            <i class="bx bx-dots-horizontal"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="avatar avatar-xl mx-auto mb-3">
                        <span class="avatar-initial rounded-circle bg-label-primary">
                            <i class="bx bx-server fs-2"></i>
                        </span>
                    </div>
                    <h5>Belum ada server</h5>
                    <p class="text-muted mb-4">Tambahkan server pertama untuk memulai</p>
                    <a href="{{ route('admin.servers.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>Tambah Server
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Password Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Server Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Password:</label>
                    <div class="input-group">
                        <input type="password" id="serverPassword" class="form-control" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility()">
                            <i class="bx bx-eye" id="passwordToggleIcon"></i>
                        </button>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyPassword()">
                            <i class="bx bx-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Modal -->
<div class="modal fade" id="actionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="actionModalTitle">Server Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary text-start" onclick="showPasswordFromAction()">
                        <i class="bx bx-lock-open me-2"></i>Show Password
                    </button>
                    <a id="loginLinkBtn" href="#" target="_blank" class="btn btn-outline-info text-start">
                        <i class="bx bx-link me-2"></i>Login to Server
                    </a>
                    <hr class="my-2">
                    <a id="editLinkBtn" href="#" class="btn btn-outline-secondary text-start">
                        <i class="bx bx-edit me-2"></i>Edit Server
                    </a>
                    <button type="button" class="btn btn-outline-warning text-start" onclick="toggleStatusFromAction()">
                        <i class="bx bx-power-off me-2"></i>Toggle Status
                    </button>
                    <button type="button" class="btn btn-outline-danger text-start" onclick="deleteServerFromAction()">
                        <i class="bx bx-trash me-2"></i>Delete Server
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copied to clipboard!', 'success');
    });
}

// Show password modal
function showPassword(serverId) {
    fetch(`/admin/servers/${serverId}/password`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('serverPassword').value = data.password;
                const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
                modal.show();
            } else {
                showToast('Failed to get password', 'danger');
            }
        })
        .catch(error => {
            showToast('Error getting password', 'danger');
        });
}

// Toggle password visibility
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('serverPassword');
    const icon = document.getElementById('passwordToggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.className = 'bx bx-eye-off';
    } else {
        passwordInput.type = 'password';
        icon.className = 'bx bx-eye';
    }
}

// Copy password
function copyPassword() {
    const passwordInput = document.getElementById('serverPassword');
    copyToClipboard(passwordInput.value);
}

// Toggle server status
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

// Action Modal Variables
let currentServerId = null;
let currentServerName = '';

// Open action modal
function openActionModal(serverId, serverName, loginLink, editUrl) {
    currentServerId = serverId;
    currentServerName = serverName;
    
    document.getElementById('actionModalTitle').textContent = `Actions - ${serverName}`;
    document.getElementById('loginLinkBtn').href = loginLink;
    document.getElementById('editLinkBtn').href = editUrl;
    
    const modal = new bootstrap.Modal(document.getElementById('actionModal'));
    modal.show();
}

// Show password from action modal
function showPasswordFromAction() {
    // Close action modal
    const actionModal = bootstrap.Modal.getInstance(document.getElementById('actionModal'));
    actionModal.hide();
    
    // Show password
    setTimeout(() => {
        showPassword(currentServerId);
    }, 300);
}

// Toggle status from action modal
function toggleStatusFromAction() {
    if (confirm(`Apakah Anda yakin ingin mengubah status server ${currentServerName}?`)) {
        const actionModal = bootstrap.Modal.getInstance(document.getElementById('actionModal'));
        actionModal.hide();
        
        setTimeout(() => {
            toggleStatus(currentServerId);
        }, 300);
    }
}

// Delete server from action modal
function deleteServerFromAction() {
    if (confirm(`Apakah Anda yakin ingin menghapus server ${currentServerName}?`)) {
        const actionModal = bootstrap.Modal.getInstance(document.getElementById('actionModal'));
        actionModal.hide();
        
        setTimeout(() => {
            // Create and submit a delete form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/servers/${currentServerId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }, 300);
    }
}
</script>
@endsection
