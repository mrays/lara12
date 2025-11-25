@extends('layouts.sneat-dashboard')

@section('title', 'Tambah Register Domain')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.domain-registers.index') }}">Domain Register</a>
            </li>
            <li class="breadcrumb-item active">Tambah Register</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-0">
                        <i class="bx bx-plus me-2"></i>Tambah Register Domain Baru
                    </h5>
                    <p class="text-muted mb-0">Tambahkan register domain baru ke sistem</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Register</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.domain-registers.store') }}">
                        @csrf
                        
                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Register <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                   id="username" name="username" value="{{ old('username') }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" value="{{ old('password') }}" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility()">
                                    <i class="bx bx-eye" id="passwordToggleIcon"></i>
                                </button>
                            </div>
                            <div class="form-text">Password akan dienkripsi secara otomatis</div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Login Link -->
                        <div class="mb-3">
                            <label for="login_link" class="form-label">Login Link <span class="text-danger">*</span></label>
                            <input type="url" class="form-control @error('login_link') is-invalid @enderror" 
                                   id="login_link" name="login_link" value="{{ old('login_link') }}" required
                                   placeholder="https://example.com/login">
                            @error('login_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Expired Date -->
                        <div class="mb-3">
                            <label for="expired_date" class="form-label">Tanggal Expired <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('expired_date') is-invalid @enderror" 
                                   id="expired_date" name="expired_date" value="{{ old('expired_date') }}" required>
                            @error('expired_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
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
                            <a href="{{ route('admin.domain-registers.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Simpan Register
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
                    <h6 class="fw-semibold">Informasi Register Domain:</h6>
                    <ul class="mb-3">
                        <li><strong>Nama Register:</strong> Nama perusahaan atau layanan register domain</li>
                        <li><strong>Username:</strong> Username untuk login ke panel register</li>
                        <li><strong>Password:</strong> Password untuk login (akan dienkripsi)</li>
                        <li><strong>Login Link:</strong> URL untuk mengakses panel register</li>
                        <li><strong>Expired Date:</strong> Tanggal kadaluarsa akun register</li>
                    </ul>

                    <h6 class="fw-semibold">Status:</h6>
                    <ul class="mb-3">
                        <li><span class="badge bg-success">Active</span> - Register aktif dan bisa digunakan</li>
                        <li><span class="badge bg-warning">Suspended</span> - Register ditangguhkan</li>
                        <li><span class="badge bg-danger">Expired</span> - Register sudah kadaluarsa</li>
                    </ul>

                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>
                        <small>Password akan dienkripsi secara otomatis untuk keamanan data.</small>
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

// Set minimum date to today
document.addEventListener('DOMContentLoaded', function() {
    const expiredDateInput = document.getElementById('expired_date');
    const today = new Date().toISOString().split('T')[0];
    expiredDateInput.setAttribute('min', today);
});
</script>
@endsection
