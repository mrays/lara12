@extends('layouts.sneat-dashboard')

@section('title', 'Domain Register Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Domain Register Management</li>
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
                                <i class="bx bx-globe me-2"></i>Domain Register Management
                            </h5>
                            <p class="text-muted mb-0">Kelola register domain dan informasi login</p>
                        </div>
                        <a href="{{ route('admin.domain-registers.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Tambah Register
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
                            <i class="bx bx-globe fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $statusCounts['all'] }}</h4>
                            <small class="text-muted">Total Register</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.domain-registers.index') }}">
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
                    <div class="col-md-7">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Cari nama register, username..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('admin.domain-registers.index') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Registers List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Register Domain</h5>
            <button type="button" class="btn btn-danger btn-sm" id="deleteSelectedBtn" style="display: none;" onclick="deleteSelected()">
                <i class="bx bx-trash me-1"></i>Delete Selected (<span id="selectedCount">0</span>)
            </button>
        </div>
        <div class="card-body">
            @if($registers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" class="form-check-input" id="selectAll" onclick="toggleSelectAll()">
                                </th>
                                <th>Nama Register</th>
                                <th>Username</th>
                                <th>Login Link</th>
                                <th>Expired Date</th>
                                <th>Status</th>
                                <th>Clients</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registers as $register)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input register-checkbox" value="{{ $register->id }}" onclick="updateSelectedCount()">
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $register->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $register->notes }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span>{{ $register->username }}</span>
                                            <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $register->username }}')" title="Copy">
                                                <i class="bx bx-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="{{ $register->login_link }}" target="_blank" class="text-decoration-none">
                                                <i class="bx bx-link me-1"></i>
                                                <small>{{ Str::limit($register->login_link, 30) }}</small>
                                            </a>
                                            <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $register->login_link }}')" title="Copy">
                                                <i class="bx bx-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="{{ $register->isExpired() ? 'text-danger' : ($register->isExpiringSoon() ? 'text-warning' : 'text-success') }}">
                                                {{ $register->expired_date->format('M d, Y') }}
                                            </span>
                                            @if($register->isExpired())
                                                <span class="badge bg-danger ms-2">Expired</span>
                                            @elseif($register->isExpiringSoon())
                                                <span class="badge bg-warning ms-2">Expiring Soon</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $register->status_badge_class }}">
                                            {{ ucfirst($register->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-info">{{ $register->clients->count() }}</span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-horizontal"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="showPassword({{ $register->id }})">
                                                        <i class="bx bx-lock-open me-2"></i>Show Password
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ $register->login_link }}" target="_blank">
                                                        <i class="bx bx-link me-2"></i>Login to Register
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.domain-registers.edit', $register) }}">
                                                        <i class="bx bx-edit me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item" onclick="toggleStatus({{ $register->id }})">
                                                        <i class="bx bx-power-off me-2"></i>Toggle Status
                                                    </button>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.domain-registers.destroy', $register) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus register ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bx bx-trash me-2"></i>Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
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
                            <i class="bx bx-globe fs-2"></i>
                        </span>
                    </div>
                    <h5>Belum ada register domain</h5>
                    <p class="text-muted mb-4">Tambahkan register domain pertama untuk memulai</p>
                    <a href="{{ route('admin.domain-registers.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>Tambah Register
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
                <h5 class="modal-title">Register Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Password:</label>
                    <div class="input-group">
                        <input type="password" id="registerPassword" class="form-control" readonly>
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

<script>
// Copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copied to clipboard!', 'success');
    });
}

// Show password modal
function showPassword(registerId) {
    fetch(`/admin/domain-registers/${registerId}/password`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('registerPassword').value = data.password;
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
    const passwordInput = document.getElementById('registerPassword');
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
    const passwordInput = document.getElementById('registerPassword');
    copyToClipboard(passwordInput.value);
}

// Toggle register status
function toggleStatus(registerId) {
    fetch(`/admin/domain-registers/${registerId}/toggle-status`, {
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

// Toggle select all checkboxes
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.register-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateSelectedCount();
}

// Update selected count
function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.register-checkbox:checked');
    const count = checkboxes.length;
    const deleteBtn = document.getElementById('deleteSelectedBtn');
    const countSpan = document.getElementById('selectedCount');
    
    countSpan.textContent = count;
    deleteBtn.style.display = count > 0 ? 'block' : 'none';
    
    // Update select all checkbox state
    const allCheckboxes = document.querySelectorAll('.register-checkbox');
    const selectAllCheckbox = document.getElementById('selectAll');
    selectAllCheckbox.checked = count === allCheckboxes.length && count > 0;
}

// Delete selected registers
function deleteSelected() {
    const checkboxes = document.querySelectorAll('.register-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    if (ids.length === 0) {
        showToast('Pilih minimal satu item untuk dihapus', 'warning');
        return;
    }
    
    if (!confirm(`Apakah Anda yakin ingin menghapus ${ids.length} register yang dipilih?`)) {
        return;
    }
    
    fetch('/admin/domain-registers/bulk-delete', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ ids: ids })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Gagal menghapus register', 'danger');
        }
    })
    .catch(error => {
        showToast('Error menghapus register', 'danger');
    });
}
</script>
@endsection
