@extends('layouts.sneat-dashboard')

@section('title', 'Gmail API Settings')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active">Gmail API</li>
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
                                <i class="bx bx-envelope me-2"></i>Gmail API Settings
                            </h5>
                            <p class="text-muted mb-0">Kelola autentikasi Gmail OAuth2 untuk pengiriman email</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Status Card -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bx bx-info-circle me-2"></i>Status Autentikasi</h6>
                </div>
                <div class="card-body">
                    @if($isAuthenticated)
                        <div class="alert alert-success mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-check-circle fs-3 me-3"></i>
                                <div>
                                    <h6 class="mb-0">Terautentikasi</h6>
                                    <small>Gmail API siap digunakan untuk mengirim email</small>
                                </div>
                            </div>
                        </div>

                        @if($tokenInfo)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Refresh Token</strong></td>
                                        <td>
                                            @if($tokenInfo['has_refresh_token'])
                                                <span class="badge bg-success">Tersedia</span>
                                                <small class="text-muted d-block">Auto-refresh aktif</small>
                                            @else
                                                <span class="badge bg-warning">Tidak tersedia</span>
                                                <small class="text-muted d-block">Perlu re-authenticate</small>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Auto-Refresh</strong></td>
                                        <td>
                                            @if($tokenInfo['can_auto_refresh'])
                                                <span class="badge bg-success">Aktif</span>
                                                <small class="text-success d-block">Token tidak akan expired</small>
                                            @else
                                                <span class="badge bg-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        @endif

                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('admin.settings.gmail.auth') }}" class="btn btn-outline-primary">
                                <i class="bx bx-refresh me-1"></i>Re-authenticate
                            </a>
                            <form action="{{ route('admin.settings.gmail.revoke') }}" method="POST" class="d-inline" 
                                  onsubmit="return confirm('Yakin ingin mencabut autentikasi? Email tidak akan bisa dikirim sampai Anda authenticate ulang.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bx bx-x-circle me-1"></i>Revoke Token
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-warning mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-error fs-3 me-3"></i>
                                <div>
                                    <h6 class="mb-0">Belum Terautentikasi</h6>
                                    <small>Klik tombol di bawah untuk menghubungkan dengan Gmail API</small>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('admin.settings.gmail.auth') }}" class="btn btn-primary">
                            <i class="bx bx-link me-1"></i>Authenticate dengan Google
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Test Email Card -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bx bx-send me-2"></i>Test Kirim Email</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Kirim email test untuk memastikan konfigurasi berfungsi dengan baik.</p>
                    
                    <form action="{{ route('admin.settings.gmail.test') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="test_email" class="form-label">Email Tujuan</label>
                            <input type="email" class="form-control" id="test_email" name="email" 
                                   placeholder="contoh@email.com" required {{ !$isAuthenticated ? 'disabled' : '' }}>
                        </div>
                        <button type="submit" class="btn btn-success" {{ !$isAuthenticated ? 'disabled' : '' }}>
                            <i class="bx bx-paper-plane me-1"></i>Kirim Test Email
                        </button>
                        @if(!$isAuthenticated)
                            <small class="text-danger d-block mt-2">
                                <i class="bx bx-info-circle me-1"></i>Authenticate terlebih dahulu untuk mengirim email
                            </small>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration Info -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bx bx-cog me-2"></i>Konfigurasi OAuth2</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Client ID</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ substr(config('services.google.client_id'), 0, 40) }}..." readonly>
                                    <span class="input-group-text"><i class="bx bx-lock"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Redirect URI</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ config('services.google.redirect_uri') }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ config('services.google.redirect_uri') }}')">
                                        <i class="bx bx-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mb-0">
                        <h6 class="alert-heading"><i class="bx bx-bulb me-1"></i>Catatan Penting</h6>
                        <ul class="mb-0 ps-3">
                            <li>Pastikan Redirect URI sudah ditambahkan di <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="alert-link">Google Cloud Console</a></li>
                            <li>Gmail API harus diaktifkan di project Google Cloud</li>
                            <li>Setelah authenticate, token akan otomatis di-refresh sehingga tidak perlu re-authenticate</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Copied to clipboard!');
    });
}
</script>
@endsection
