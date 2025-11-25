@extends('layouts.sneat-dashboard')

@section('title', 'Edit Profile')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Edit Profile</li>
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
                                <i class="bx bx-user me-2"></i>Edit Profile
                            </h5>
                            <p class="text-muted mb-0">Update your personal information and account settings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="avatar avatar-xl mb-3">
                        <img src="{{ asset('vendor/sneat/assets/img/avatars/1.png') }}" alt="Profile" class="rounded-circle">
                    </div>
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <div class="d-flex justify-content-center gap-2">
                        <span class="badge bg-label-primary">
                            <i class="bx bx-user me-1"></i>{{ ucfirst($user->role) }}
                        </span>
                        <span class="badge bg-label-success">
                            <i class="bx bx-check-circle me-1"></i>Active
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Account Information</h6>
                    <div class="mb-2">
                        <small class="text-muted">Member Since:</small>
                        <div class="fw-semibold">{{ $user->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Last Updated:</small>
                        <div class="fw-semibold">{{ $user->updated_at->format('M d, Y') }}</div>
                    </div>
                    <div>
                        <small class="text-muted">Account ID:</small>
                        <div class="fw-semibold">#{{ $user->id }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-user me-2"></i>Personal Information
                    </h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bx bx-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        
                        <div class="row">
                            <!-- Nama (Wajib) -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <i class="bx bx-user me-1"></i>Full Name
                                </label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="text-danger small mt-1">
                                        <i class="bx bx-error-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Email (Wajib) -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="bx bx-envelope me-1"></i>Email Address
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="text-danger small mt-1">
                                        <i class="bx bx-error-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- WhatsApp (Wajib) -->
                            <div class="col-md-6 mb-3">
                                <label for="whatsapp" class="form-label">
                                    <i class="bx bx-phone me-1"></i>WhatsApp Number
                                </label>
                                <input type="tel" class="form-control" id="whatsapp" name="whatsapp" 
                                       value="{{ old('whatsapp', $user->whatsapp) }}" required>
                                @error('whatsapp')
                                    <div class="text-danger small mt-1">
                                        <i class="bx bx-error-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Business Name (Optional) -->
                            <div class="col-md-6 mb-3">
                                <label for="business_name" class="form-label">
                                    <i class="bx bx-building me-1"></i>Business Name
                                </label>
                                <input type="text" class="form-control" id="business_name" name="business_name" 
                                       value="{{ old('business_name', $user->business_name) }}" 
                                       placeholder="Optional">
                                @error('business_name')
                                    <div class="text-danger small mt-1">
                                        <i class="bx bx-error-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Address (Optional) -->
                            <div class="col-12 mb-3">
                                <label for="address" class="form-label">
                                    <i class="bx bx-map me-1"></i>Address
                                </label>
                                <textarea class="form-control" id="address" name="address" rows="3" 
                                          placeholder="Optional">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="text-danger small mt-1">
                                        <i class="bx bx-error-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-lock me-2"></i>Change Password
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password.update') }}">
                        @csrf
                        
                        <div class="row">
                            <!-- Current Password -->
                            <div class="col-md-4 mb-3">
                                <label for="current_password" class="form-label">
                                    <i class="bx bx-lock me-1"></i>Current Password
                                </label>
                                <input type="password" class="form-control" id="current_password" 
                                       name="current_password" required>
                                @error('current_password')
                                    <div class="text-danger small mt-1">
                                        <i class="bx bx-error-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div class="col-md-4 mb-3">
                                <label for="password" class="form-label">
                                    <i class="bx bx-lock-open me-1"></i>New Password
                                </label>
                                <input type="password" class="form-control" id="password" 
                                       name="password" required>
                                @error('password')
                                    <div class="text-danger small mt-1">
                                        <i class="bx bx-error-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-4 mb-3">
                                <label for="password_confirmation" class="form-label">
                                    <i class="bx bx-lock-open me-1"></i>Confirm Password
                                </label>
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="bx bx-refresh me-1"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Password strength indicator
document.getElementById('password')?.addEventListener('input', function(e) {
    const password = e.target.value;
    const strength = checkPasswordStrength(password);
    updatePasswordStrengthIndicator(strength);
});

function checkPasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]+/)) strength++;
    if (password.match(/[A-Z]+/)) strength++;
    if (password.match(/[0-9]+/)) strength++;
    if (password.match(/[$@#&!]+/)) strength++;
    return strength;
}

function updatePasswordStrengthIndicator(strength) {
    const indicator = document.getElementById('password-strength');
    if (!indicator) return;
    
    const messages = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
    const colors = ['danger', 'warning', 'info', 'success', 'success'];
    
    indicator.className = `form-text text-${colors[strength]}`;
    indicator.textContent = `Password strength: ${messages[strength]}`;
}
</script>
@endsection
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       placeholder="Masukkan nama lengkap" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email (Wajib) -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    Alamat Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       placeholder="contoh@email.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- WhatsApp (Wajib) -->
                            <div class="col-md-6 mb-3">
                                <label for="whatsapp" class="form-label">
                                    Nomor WhatsApp <span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control @error('whatsapp') is-invalid @enderror" 
                                       id="whatsapp" name="whatsapp" 
                                       value="{{ old('whatsapp', $user->whatsapp) }}" 
                                       placeholder="08xxxxxxxxxx" required>
                                @error('whatsapp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Format: 08xxxxxxxxxx (tanpa +62)</div>
                            </div>

                            <!-- Nama Usaha (Opsional) -->
                            <div class="col-md-6 mb-3">
                                <label for="business_name" class="form-label">
                                    Nama Usaha
                                </label>
                                <input type="text" class="form-control @error('business_name') is-invalid @enderror" 
                                       id="business_name" name="business_name" 
                                       value="{{ old('business_name', $user->business_name) }}" 
                                       placeholder="Nama perusahaan/usaha (opsional)">
                                @error('business_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Alamat Rumah (Opsional) -->
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">
                                    Alamat Rumah
                                </label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3"
                                          placeholder="Alamat lengkap (opsional)">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="bx bx-info-circle me-2"></i>
                                    <strong>Catatan:</strong> Field yang bertanda <span class="text-danger">*</span> wajib diisi.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bx bx-lock me-2"></i>Ganti Password
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password.update') }}">
                        @csrf
                        
                        <div class="row">
                            <!-- Password Saat Ini -->
                            <div class="col-md-12 mb-3">
                                <label for="current_password" class="form-label">
                                    Password Saat Ini <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-merge">
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" name="current_password" 
                                           placeholder="Masukkan password saat ini" required>
                                    <span class="input-group-text cursor-pointer" onclick="togglePassword('current_password')">
                                        <i class="bx bx-hide" id="current_password_icon"></i>
                                    </span>
                                </div>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Password Baru -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    Password Baru <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-merge">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" 
                                           placeholder="Masukkan password baru" required>
                                    <span class="input-group-text cursor-pointer" onclick="togglePassword('password')">
                                        <i class="bx bx-hide" id="password_icon"></i>
                                    </span>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Minimal 8 karakter</div>
                            </div>

                            <!-- Konfirmasi Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">
                                    Konfirmasi Password Baru <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-merge">
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                           id="password_confirmation" name="password_confirmation" 
                                           placeholder="Ulangi password baru" required>
                                    <span class="input-group-text cursor-pointer" onclick="togglePassword('password_confirmation')">
                                        <i class="bx bx-hide" id="password_confirmation_icon"></i>
                                    </span>
                                </div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="bx bx-info-circle me-2"></i>
                                    <strong>Perhatian:</strong> Setelah mengganti password, Anda akan tetap login di sesi ini. Pastikan Anda mengingat password baru.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="bx bx-key me-1"></i> Ganti Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.remove('bx-hide');
        icon.classList.add('bx-show');
    } else {
        passwordField.type = 'password';
        icon.classList.remove('bx-show');
        icon.classList.add('bx-hide');
    }
}

// Auto-hide success messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-success');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert.querySelector('.btn-close')) {
                alert.querySelector('.btn-close').click();
            }
        }, 5000);
    });
});
</script>
@endpush

@endsection
