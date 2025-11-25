@extends('layouts.sneat-dashboard')

@section('title', 'Domain Assignment Guide')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.domain-expiration.index') }}">Domain Expiration</a>
            </li>
            <li class="breadcrumb-item active">Assignment Guide</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-0">
                        <i class="bx bx-book-reader me-2"></i>Panduan: Cara Menerapkan Domain ke Client
                    </h5>
                    <p class="text-muted mb-0">Langkah demi langkah untuk mengelola domain client</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Guide Steps -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Step-by-Step Guide</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Step 1 -->
                        <div class="timeline-item">
                            <div class="timeline-point timeline-point-primary">
                                <i class="bx bx-hash"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">Step 1: Tambah Domain Register</h6>
                                    <a href="{{ route('admin.domain-registers.create') }}" class="btn btn-sm btn-primary">
                                        <i class="bx bx-plus me-1"></i>Add Domain Register
                                    </a>
                                </div>
                                <p class="text-muted mb-3">
                                    Pertama, tambahkan domain register yang akan digunakan oleh client. Ini adalah tempat dimana domain dibeli (GoDaddy, Namecheap, dll).
                                </p>
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="bx bx-info-circle me-2"></i>Informasi yang Diperlukan:
                                    </h6>
                                    <ul class="mb-0">
                                        <li><strong>Nama Register:</strong> Nama penyedia domain (contoh: GoDaddy, Namecheap)</li>
                                        <li><strong>Login Link:</strong> URL untuk login ke panel domain</li>
                                        <li><strong>Expired Date:</strong> Tanggal kadaluarsa register (bukan domain client)</li>
                                        <li><strong>Username/Password:</strong> Kredensial untuk akses panel</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="timeline-item">
                            <div class="timeline-point timeline-point-success">
                                <i class="bx bx-user-plus"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">Step 2: Tambah Client Data</h6>
                                    <a href="{{ route('admin.client-data.create') }}" class="btn btn-sm btn-success">
                                        <i class="bx bx-plus me-1"></i>Add Client
                                    </a>
                                </div>
                                <p class="text-muted mb-3">
                                    Tambahkan data client beserta informasi domain dan layanan mereka.
                                </p>
                                <div class="alert alert-success">
                                    <h6 class="alert-heading">
                                        <i class="bx bx-check-circle me-2"></i>Field Penting untuk Domain:
                                    </h6>
                                    <ul class="mb-0">
                                        <li><strong>Client Info:</strong> Nama, alamat, WhatsApp client</li>
                                        <li><strong>Domain Register:</strong> Pilih register yang sudah dibuat</li>
                                        <li><strong>Domain Expired:</strong> Tanggal kadaluarsa domain client</li>
                                        <li><strong>Website Service:</strong> Tanggal expired layanan website</li>
                                        <li><strong>Hosting Expired:</strong> Tanggal expired hosting</li>
                                    </ul>
                                </div>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bx bx-sync me-2"></i>Auto-Sync Feature
                                        </h6>
                                        <p class="card-text">
                                            Saat memilih domain register, sistem akan otomatis mengisi tanggal expired. 
                                            Website service expiration akan otomatis mengikuti domain expiration.
                                        </p>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-secondary" disabled>
                                                <i class="bx bx-sync"></i> Sync dengan Domain
                                            </button>
                                            <small class="text-muted">Tersedia di form edit client</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="timeline-item">
                            <div class="timeline-point timeline-point-warning">
                                <i class="bx bx-time-five"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Step 3: Monitor Domain Expiration</h6>
                                <p class="text-muted mb-3">
                                    Setelah client ditambahkan, monitor domain expiration melalui menu Domain Expiration.
                                </p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="alert alert-warning">
                                            <h6 class="alert-heading">
                                                <i class="bx bx-bell me-2"></i>Monitoring Features:
                                            </h6>
                                            <ul class="mb-0">
                                                <li><strong>3 Bulan Warning:</strong> Peringatan untuk domain akan expired</li>
                                                <li><strong>7 Hari Critical:</strong> Alert khusus untuk domain expired dalam 7 hari</li>
                                                <li><strong>Expired Status:</strong> Domain yang sudah kadaluarsa</li>
                                                <li><strong>WhatsApp Integration:</strong> Kirim reminder langsung ke client</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-warning">
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    <i class="bx bx-chart me-2"></i>Statistics Dashboard
                                                </h6>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Total Domains:</span>
                                                    <span class="badge bg-primary">{{ \App\Models\ClientData::count() }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Expired:</span>
                                                    <span class="badge bg-danger">{{ \App\Models\ClientData::where('domain_expired', '<', now())->count() }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Expiring Soon:</span>
                                                    <span class="badge bg-warning">{{ \App\Models\ClientData::where('domain_expired', '>=', now())->where('domain_expired', '<=', now()->addMonths(3))->count() }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Safe:</span>
                                                    <span class="badge bg-success">{{ \App\Models\ClientData::where('domain_expired', '>', now()->addMonths(3))->count() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="timeline-item">
                            <div class="timeline-point timeline-point-info">
                                <i class="bx bx-message-rounded-detail"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Step 4: Kirim Reminder ke Client</h6>
                                <p class="text-muted mb-3">
                                    Gunakan WhatsApp integration untuk mengirim reminder renewal ke client.
                                </p>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bx bx-whatsapp me-2"></i>WhatsApp Template Message
                                        </h6>
                                        <div class="bg-white border rounded p-3 mb-3">
                                            <pre class="mb-0">Halo [Client Name],

Ini adalah pengingat bahwa domain Anda akan segera expired:
📅 Tanggal Expired: [Date]
⏰ [X] hari lagi

Silakan lakukan perpanjangan sebelum tanggal kadaluarsa untuk menghindari downtime.

Terima kasih.</pre>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-success" disabled>
                                                <i class="bx bx-bell me-1"></i>Send Reminder
                                            </button>
                                            <button class="btn btn-sm btn-outline-success" disabled>
                                                <i class="bx bx-message-rounded-detail me-1"></i>Open WhatsApp
                                            </button>
                                            <small class="text-muted">Tersedia di Domain Expiration table</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-run me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="d-grid">
                                <a href="{{ route('admin.domain-registers.create') }}" class="btn btn-primary">
                                    <i class="bx bx-plus me-2"></i>Add Domain Register
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-grid">
                                <a href="{{ route('admin.client-data.create') }}" class="btn btn-success">
                                    <i class="bx bx-user-plus me-2"></i>Add Client Data
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-grid">
                                <a href="{{ route('admin.domain-expiration.index') }}" class="btn btn-warning">
                                    <i class="bx bx-time-five me-2"></i>Monitor Expiration
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-grid">
                                <a href="{{ route('admin.domain-expiration.index') }}?filter=expiring" class="btn btn-info">
                                    <i class="bx bx-bell me-2"></i>View Expiring Soon
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 40px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-point {
    position: absolute;
    left: -35px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
}

.timeline-content {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
</style>
@endsection
