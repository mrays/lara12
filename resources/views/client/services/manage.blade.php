@extends('layouts.sneat-dashboard')

@section('title', 'Kelola Layanan - ' . ($service->product ?? 'Layanan'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('client.services.index') }}">Layanan Saya</a>
            </li>
            <li class="breadcrumb-item active">{{ $service->product ?? 'Layanan' }}</li>
        </ol>
    </nav>

    <!-- Service Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-wrapper me-3">
                                <div class="avatar avatar-lg">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        <i class="bx bx-globe fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div>
                                <h4 class="mb-1">{{ $service->product ?? 'Business Website Exclusive Type M' }}</h4>
                                <p class="mb-0 text-muted">{{ $service->domain ?? 'websiteload' }}</p>
                            </div>
                        </div>
                        <div class="text-end">
                            @if($service->status === 'Active')
                                <span class="badge bg-label-success fs-6 mb-2">ACTIVE</span>
                            @else
                                @php
                                    $progressValue = match($service->status) {
                                        'Active' => 100,
                                        'Pending' => 30,
                                        'Suspended' => 0,
                                        'Terminated' => 0,
                                        'Dibatalkan' => 0,
                                        'Disuspen' => 0,
                                        'Sedang Dibuat' => 75,
                                        'Ditutup' => 0,
                                        default => 10
                                    };
                                    $progressColor = match($service->status) {
                                        'Active' => 'success',
                                        'Pending' => 'warning',
                                        'Suspended' => 'secondary',
                                        'Terminated' => 'secondary',
                                        'Dibatalkan' => 'secondary',
                                        'Disuspen' => 'secondary',
                                        'Sedang Dibuat' => 'primary',
                                        'Ditutup' => 'secondary',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-label-{{ $progressColor }} fs-6 mb-2">{{ strtoupper($service->status) }}</span>
                                <div class="progress mt-2" style="height: 6px; width: 150px;">
                                    <div class="progress-bar {{ $progressValue > 0 ? 'progress-bar-striped progress-bar-animated' : '' }} bg-{{ $progressColor }}" 
                                         role="progressbar" 
                                         style="width: {{ $progressValue }}%"
                                         aria-valuenow="{{ $progressValue }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-{{ $progressColor }} d-block mt-1">{{ $progressValue }}% Selesai</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Management Tabs -->
    <div class="row">
        <div class="col-md-3">
            <!-- Sidebar Menu -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="nav-align-left">
                        <ul class="nav nav-pills flex-column" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active w-100 text-start" 
                                        id="overview-tab" data-bs-toggle="pill" 
                                        data-bs-target="#overview" type="button" 
                                        role="tab" aria-controls="overview" aria-selected="true">
                                    <i class="bx bx-star me-2"></i>Ringkasan
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link w-100 text-start" 
                                        id="information-tab" data-bs-toggle="pill" 
                                        data-bs-target="#information" type="button" 
                                        role="tab" aria-controls="information" aria-selected="false">
                                    <i class="bx bx-info-circle me-2"></i>Informasi
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link w-100 text-start" 
                                        id="actions-tab" data-bs-toggle="pill" 
                                        data-bs-target="#actions" type="button" 
                                        role="tab" aria-controls="actions" aria-selected="false">
                                    <i class="bx bx-cog me-2"></i>Aksi
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Service Categories -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">LAYANAN</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bx bx-globe me-2"></i>Website</span>
                            <span class="badge bg-primary rounded-pill">{{ $service->status === 'Active' ? '1' : '0' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bx bx-dots-horizontal me-2"></i>Segera Hadir</span>
                            <span class="badge bg-secondary rounded-pill">0</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Billing -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">TAGIHAN</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="{{ route('client.invoices.index') }}" class="text-decoration-none">
                                <i class="bx bx-receipt me-2"></i>Tagihan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Support -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">BANTUAN</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="#" class="text-decoration-none" onclick="contactSupport()">
                                <i class="bx bx-message-dots me-2"></i>WhatsApp Kami
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ $service->product ?? 'Business Website Exclusive Type M' }}</h5>
                        </div>
                        <div class="card-body">
                            <!-- Service Details -->
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold text-muted">Nama Pengguna</label>
                                    <div class="input-group">
                                        @if($service->status === 'Active' && !empty($service->username))
                                            <input type="text" class="form-control" value="{{ $service->username }}" readonly>
                                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $service->username }}')">
                                                <i class="bx bx-copy"></i>
                                            </button>
                                        @else
                                            <input type="text" class="form-control" value="" placeholder="Belum diatur" readonly>
                                            <button class="btn btn-outline-secondary" type="button" disabled>
                                                <i class="bx bx-copy"></i>
                                            </button>
                                        @endif
                                    </div>
                                    @if($service->status !== 'Active' || empty($service->username))
                                        <small class="text-muted">Nama pengguna akan diatur oleh admin setelah layanan aktif</small>
                                    @endif
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold text-muted">Kata Sandi</label>
                                    <div class="input-group">
                                        @if($service->status === 'Active' && !empty($service->password))
                                            <input type="password" id="password-field" class="form-control" value="{{ $service->password }}" readonly>
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility()">
                                                <i class="bx bx-show" id="password-toggle-icon"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $service->password }}')">
                                                <i class="bx bx-copy"></i>
                                            </button>
                                        @else
                                            <input type="password" class="form-control" value="" placeholder="Belum diatur" readonly>
                                            <button class="btn btn-outline-secondary" type="button" disabled>
                                                <i class="bx bx-show"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" type="button" disabled>
                                                <i class="bx bx-copy"></i>
                                            </button>
                                        @endif
                                    </div>
                                    @if($service->status !== 'Active' || empty($service->password))
                                        <small class="text-muted">Kata sandi akan diatur oleh admin setelah layanan aktif</small>
                                    @endif
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold text-muted">Server</label>
                                    <input type="text" class="form-control" value="{{ $service->server ?? 'Default Server' }}" readonly>
                                </div>
                            </div>

                            <!-- Upgrade Request Status -->
                            @php
                                $pendingUpgradeRequest = \App\Models\ServiceUpgradeRequest::where('service_id', $service->id)
                                    ->where('client_id', auth()->id())
                                    ->whereIn('status', ['pending', 'approved', 'processing'])
                                    ->first();
                            @endphp
                            
                            @if($pendingUpgradeRequest)
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="alert alert-{{ $pendingUpgradeRequest->status === 'pending' ? 'warning' : ($pendingUpgradeRequest->status === 'approved' ? 'success' : 'info') }} d-flex align-items-start">
                                            <i class="bx bx-{{ $pendingUpgradeRequest->status === 'pending' ? 'time' : ($pendingUpgradeRequest->status === 'approved' ? 'check-circle' : 'cog') }} me-2 mt-1"></i>
                                            <div class="flex-grow-1">
                                                <strong>Status Permintaan Upgrade:</strong>
                                                <div class="mt-2">
                                                    <span class="badge bg-{{ $pendingUpgradeRequest->status === 'pending' ? 'warning' : ($pendingUpgradeRequest->status === 'approved' ? 'success' : 'info') }} me-2">
                                                        {{ ucfirst($pendingUpgradeRequest->status) }}
                                                    </span>
                                                    <small class="text-muted">
                                                        Permintaan #{{ $pendingUpgradeRequest->id }} diajukan pada {{ $pendingUpgradeRequest->created_at->format('d M Y') }}
                                                    </small>
                                                </div>
                                                <div class="mt-2">
                                                    <strong>Dari:</strong> {{ $pendingUpgradeRequest->current_plan }} 
                                                    <i class="bx bx-right-arrow-alt mx-2"></i>
                                                    <strong>Ke:</strong> {{ $pendingUpgradeRequest->requested_plan }}
                                                </div>
                                                
                                                @if($pendingUpgradeRequest->status === 'approved')
                                                    <div class="mt-2 text-success">
                                                        <i class="bx bx-check-circle me-1"></i>
                                                        Permintaan upgrade Anda telah disetujui! 
                                                        <a href="{{ route('client.invoices.index') }}" class="alert-link">Cek tagihan Anda</a> untuk detail pembayaran.
                                                    </div>
                                                @elseif($pendingUpgradeRequest->status === 'processing')
                                                    <div class="mt-2 text-info">
                                                        <i class="bx bx-cog me-1"></i>
                                                        Permintaan upgrade Anda sedang diproses oleh tim kami.
                                                    </div>
                                                @else
                                                    <div class="mt-2 text-warning">
                                                        <i class="bx bx-time me-1"></i>
                                                        Permintaan upgrade Anda menunggu persetujuan admin.
                                                    </div>
                                                @endif
                                                
                                                @if($pendingUpgradeRequest->admin_notes)
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            <strong>Catatan Admin:</strong> {{ $pendingUpgradeRequest->admin_notes }}
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Service Setup Progress (for non-active services) -->
                            @if($service->status !== 'Active')
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body">
                                                <h6 class="mb-3">
                                                    <i class="bx bx-cog me-2"></i>Progres Pengaturan Layanan
                                                </h6>
                                                
                                                @php
                                                    $steps = [
                                                        ['name' => 'Pesanan Diterima', 'status' => 'completed'],
                                                        ['name' => 'Verifikasi Pembayaran', 'status' => $service->status === 'Pending' ? 'current' : 'completed'],
                                                        ['name' => 'Pengaturan Server', 'status' => $service->status === 'Sedang Dibuat' ? 'current' : ($service->status === 'Pending' ? 'pending' : 'completed')],
                                                        ['name' => 'Aktivasi Layanan', 'status' => $service->status === 'Active' ? 'completed' : ($service->status === 'Sedang Dibuat' ? 'pending' : 'pending')]
                                                    ];
                                                @endphp
                                                
                                                <div class="d-flex justify-content-between align-items-center">
                                                    @foreach($steps as $index => $step)
                                                        <div class="d-flex flex-column align-items-center" style="flex: 1;">
                                                            <div class="mb-2">
                                                                @if($step['status'] === 'completed')
                                                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                        <i class="bx bx-check"></i>
                                                                    </div>
                                                                @elseif($step['status'] === 'current')
                                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                        <div class="spinner-border spinner-border-sm" role="status">
                                                                            <span class="visually-hidden">Loading...</span>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="bg-light border rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                        <i class="bx bx-time text-muted"></i>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <small class="text-center {{ $step['status'] === 'completed' ? 'text-success' : ($step['status'] === 'current' ? 'text-primary' : 'text-muted') }}">
                                                                {{ $step['name'] }}
                                                            </small>
                                                        </div>
                                                        
                                                        @if($index < count($steps) - 1)
                                                            <div class="flex-grow-1 mx-2" style="height: 2px; margin-top: -20px;">
                                                                <div class="bg-{{ $steps[$index + 1]['status'] === 'completed' ? 'success' : 'light' }}" style="height: 100%;"></div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                
                                                <div class="mt-3 text-center">
                                                    <small class="text-muted">
                                                        @switch($service->status)
                                                            @case('Pending')
                                                                Menunggu konfirmasi pembayaran. Biasanya memakan waktu 1-24 jam.
                                                                @break
                                                            @case('Sedang Dibuat')
                                                                Sedang dibuat. Biasanya memakan waktu 2-7 hari.
                                                                @break
                                                            @case('Suspended')
                                                                Layanan telah disuspen. Silakan hubungi support.
                                                                @break
                                                            @case('Terminated')
                                                                Layanan telah dihentikan.
                                                                @break
                                                            @case('Dibatalkan')
                                                                Layanan telah dibatalkan.
                                                                @break
                                                            @case('Disuspen')
                                                                Layanan telah disuspen oleh admin.
                                                                @break
                                                            @case('Ditutup')
                                                                Layanan telah ditutup.
                                                                @break
                                                            @default
                                                                Pengaturan layanan sedang berjalan. Mohon tunggu...
                                                        @endswitch
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    @php
                                        // Cek apakah layanan akan expired dalam 3 bulan
                                        $showRenewalAlert = false;
                                        $daysUntilExpiry = null;
                                        $expiryMessage = '';
                                        
                                        if ($service->due_date) {
                                            $dueDate = \Carbon\Carbon::parse($service->due_date);
                                            $threeMonthsFromNow = now()->addMonths(3);
                                            $showRenewalAlert = $dueDate->lte($threeMonthsFromNow);
                                            $daysUntilExpiry = now()->diffInDays($dueDate, false);
                                            
                                            if ($daysUntilExpiry <= 0) {
                                                $expiryMessage = 'Layanan Anda sudah expired!';
                                            } elseif ($daysUntilExpiry <= 7) {
                                                $expiryMessage = "Layanan Anda akan expired dalam {$daysUntilExpiry} hari!";
                                            } elseif ($daysUntilExpiry <= 30) {
                                                $expiryMessage = "Layanan Anda akan expired dalam {$daysUntilExpiry} hari.";
                                            } elseif ($daysUntilExpiry <= 90) {
                                                $weeks = floor($daysUntilExpiry / 7);
                                                $expiryMessage = "Layanan Anda akan expired dalam sekitar {$weeks} minggu.";
                                            }
                                        }
                                    @endphp
                                    
                                    @if($service->status === 'Active')
                                        @if($showRenewalAlert && $daysUntilExpiry !== null)
                                            <div class="alert alert-{{ $daysUntilExpiry <= 7 ? 'danger' : ($daysUntilExpiry <= 30 ? 'warning' : 'info') }} d-flex align-items-center mb-3">
                                                <i class="bx bx-{{ $daysUntilExpiry <= 7 ? 'error-circle' : 'time' }} me-2"></i>
                                                <span>
                                                    <strong>{{ $expiryMessage }}</strong> 
                                                    Segera perpanjang untuk menghindari gangguan layanan.
                                                </span>
                                            </div>
                                        @endif
                                        
                                        <div class="d-flex flex-wrap gap-2">
                                            <button class="btn btn-primary" onclick="loginDashboard('{{ $service->domain }}')">
                                                <i class="bx bx-log-in me-1"></i>Masuk Dashboard
                                            </button>
                                            <button class="btn btn-outline-success" onclick="contactSupport()">
                                                <i class="bx bx-message-dots me-1"></i>Hubungi Kami
                                            </button>
                                            @if($showRenewalAlert)
                                                <button class="btn btn-success" onclick="changePassword()">
                                                    <i class="bx bx-refresh me-1"></i>Perpanjang Sekarang
                                                </button>
                                            @endif
                                        </div>
                                    @else
                                        <div class="alert alert-warning d-flex align-items-center mb-3">
                                            <i class="bx bx-info-circle me-2"></i>
                                            <span>
                                                @if($service->status === 'Sedang Dibuat')
                                                    Layanan sedang dalam proses pembuatan. Silakan hubungi tim support untuk bantuan.
                                                @else
                                                    Layanan sedang {{ strtolower($service->status) }}. Silakan hubungi support untuk bantuan.
                                                @endif
                                            </span>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2">
                                            <button class="btn btn-warning" onclick="contactSupport()">
                                                <i class="bx bx-phone me-1"></i>Hubungi Support
                                            </button>
                                            @if($showRenewalAlert)
                                                <button class="btn btn-success" onclick="changePassword()">
                                                    <i class="bx bx-refresh me-1"></i>Perpanjang Layanan
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Tips Section -->
                            <div class="alert alert-primary d-flex align-items-start mb-4">
                                <i class="bx bx-info-circle me-2 mt-1"></i>
                                <div>
                                    <strong>Tips:</strong> Silakan klik tombol <strong>"Masuk Dashboard"</strong> untuk masuk ke Dashboard Website
                                </div>
                            </div>

                            <!-- Current Plan Section -->
                            <div class="card border">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="mb-2">Paket Saat Ini</h6>
                                            <h5 class="mb-1">Paket Anda saat ini adalah {{ $service->product ?? 'Basic' }}</h5>
                                            <p class="text-muted mb-3">
                                                @if($service->billing_cycle === 'yearly' || $service->billing_cycle === 'annually')
                                                    Berlangganan tahunan dengan nilai lebih baik
                                                @else
                                                    Paket berlangganan bulanan
                                                @endif
                                            </p>
                                            
                                            <div class="mb-3">
                                                <small class="text-muted">Aktif sampai {{ $service->due_date ? $service->due_date->format('d M Y') : '09 Des 2021' }}</small><br>
                                                <small class="text-muted">Kami akan mengirimkan notifikasi saat langganan mendekati kadaluarsa</small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <span class="badge bg-label-primary me-2">
                                                    {{ $service->price ? 'Rp ' . number_format($service->price, 0, ',', '.') : 'Rp 199.000' }} 
                                                    @if($service->billing_cycle === 'yearly' || $service->billing_cycle === 'annually')
                                                        Per Tahun
                                                    @elseif($service->billing_cycle === 'monthly')
                                                        Per Bulan
                                                    @else
                                                        /{{ $service->translated_billing_cycle }}
                                                    @endif
                                                </span>
                                                <span class="badge bg-label-info">Populer</span>
                                            </div>
                                            
                                            <p class="text-muted mb-3">
                                                @if($service->billing_cycle === 'yearly' || $service->billing_cycle === 'annually')
                                                    Paket tahunan dengan penghematan biaya untuk komitmen jangka panjang
                                                @else
                                                    Paket bulanan yang fleksibel untuk bisnis kecil hingga menengah
                                                @endif
                                            </p>
                                            
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-primary" onclick="upgradePlan()">
                                                    <i class="bx bx-up-arrow-alt me-1"></i>Upgrade Paket
                                                </button>
                                                <button class="btn btn-outline-danger" onclick="cancelSubscription()">
                                                    Batalkan Langganan
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center justify-content-center h-100">
                                                <div class="text-center">
                                                    <p class="text-muted mb-3">Kelola langganan dan upgrade kapan saja</p>
                                                    
                                                    <div class="mb-3">
                                                        <h6 class="mb-1">Status Layanan</h6>
                                                        @if($service->status === 'Active')
                                                            <div class="progress mb-2" style="height: 8px;">
                                                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                                                            </div>
                                                            <small class="text-success">Layanan Anda aktif</small>
                                                        @else
                                                            <!-- Animated Progress for Non-Active Status -->
                                                            <div class="progress mb-2" style="height: 12px;">
                                                                @php
                                                                    $progressValue = match($service->status) {
                                                                        'Active' => 100,
                                                                        'Pending' => 30,
                                                                        'Suspended' => 0,
                                                                        'Terminated' => 0,
                                                                        'Dibatalkan' => 0,
                                                                        'Disuspen' => 0,
                                                                        'Sedang Dibuat' => 75,
                                                                        'Ditutup' => 0,
                                                                        default => 10
                                                                    };
                                                                    $progressColor = match($service->status) {
                                                                        'Active' => 'success',
                                                                        'Pending' => 'warning',
                                                                        'Suspended' => 'secondary',
                                                                        'Terminated' => 'secondary',
                                                                        'Dibatalkan' => 'secondary',
                                                                        'Disuspen' => 'secondary',
                                                                        'Sedang Dibuat' => 'primary',
                                                                        'Ditutup' => 'secondary',
                                                                        default => 'secondary'
                                                                    };
                                                                @endphp
                                                                <div class="progress-bar {{ $progressValue > 0 ? 'progress-bar-striped progress-bar-animated' : '' }} bg-{{ $progressColor }}" 
                                                                     role="progressbar" 
                                                                     style="width: {{ $progressValue }}%"
                                                                     aria-valuenow="{{ $progressValue }}" 
                                                                     aria-valuemin="0" 
                                                                     aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                            <small class="text-{{ $progressColor }}">
                                                                Layanan {{ $service->status }} ({{ $progressValue }}% selesai)
                                                            </small>
                                                        @endif
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

                <!-- Information Tab -->
                <div class="tab-pane fade" id="information" role="tabpanel" aria-labelledby="information-tab">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Layanan</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Detail Layanan</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Nama Layanan:</strong></td>
                                            <td>{{ $service->product ?? 'Layanan' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Produk:</strong></td>
                                            <td>{{ $service->product }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Domain:</strong></td>
                                            <td>{{ $service->domain ?? 'Belum ditentukan' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @switch($service->status)
                                                    @case('Active')
                                                        <span class="badge bg-success">Aktif</span>
                                                        @break
                                                    @case('Suspended')
                                                        <span class="badge bg-warning">Disuspen</span>
                                                        @break
                                                    @case('Terminated')
                                                        <span class="badge bg-danger">Dihentikan</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $service->status }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dibuat:</strong></td>
                                            <td>{{ $service->created_at ? $service->created_at->format('d M Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jatuh Tempo:</strong></td>
                                            <td>{{ $service->due_date ? $service->due_date->format('d M Y') : '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Informasi Tagihan</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Siklus Tagihan:</strong></td>
                                            <td>{{ $service->translated_billing_cycle }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Harga:</strong></td>
                                            <td>Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Biaya Setup:</strong></td>
                                            <td>Rp {{ number_format($service->setup_fee ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Tab -->
                <div class="tab-pane fade" id="actions" role="tabpanel" aria-labelledby="actions-tab">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Aksi Tersedia</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Manajemen Layanan</h6>
                                    <div class="list-group">
                                        <a href="#" class="list-group-item list-group-item-action" onclick="upgradePlan()">
                                            <i class="bx bx-up-arrow-alt me-2"></i>Upgrade Layanan
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action" onclick="changePassword()">
                                            <i class="bx bx-calendar-plus me-2"></i>Perpanjang Layanan
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Bantuan</h6>
                                    <div class="list-group">
                                        <a href="#" class="list-group-item list-group-item-action" onclick="contactSupport()">
                                            <i class="bx bx-message-dots me-2"></i>Hubungi Support
                                        </a>
                                        <a href="{{ route('client.invoices.index') }}" class="list-group-item list-group-item-action">
                                            <i class="bx bx-receipt me-2"></i>Lihat Tagihan
                                        </a>
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

<!-- Upgrade Plan Modal -->
<div class="modal fade" id="upgradePlanModal" tabindex="-1" aria-labelledby="upgradePlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="upgradePlanModalLabel">Daftar Paket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <p class="text-muted">Semua paket mencakup 40+ fitur dan alat canggih untuk meningkatkan produk Anda. Pilih paket terbaik sesuai kebutuhan Anda.</p>
                    
                    <!-- Billing Info -->
                    <div class="d-flex align-items-center justify-content-center mb-4">
                        <span class="text-primary fw-semibold">Tahunan</span>
                        <span class="badge bg-label-success ms-2">Nilai Terbaik</span>
                        <input type="hidden" id="billingToggle" value="annually">
                    </div>
                </div>

                <!-- Pricing Cards -->
                <div class="row g-4">
                    @foreach($servicePackages as $index => $package)
                    <div class="col-lg-4">
                        <div class="card border {{ $index === 1 ? 'border-primary' : '' }} h-100 position-relative">
                            @if($index === 1)
                                <div class="position-absolute top-0 start-50 translate-middle">
                                    <span class="badge bg-primary">Populer</span>
                                </div>
                            @endif
                            
                            <div class="card-body text-center p-4">
                                <!-- Plan Icon -->
                                <div class="mb-4">
                                    <div class="avatar avatar-xl mx-auto">
                                        <span class="avatar-initial rounded-circle bg-label-{{ $index === 0 ? 'primary' : ($index === 1 ? 'success' : 'info') }}">
                                            <i class="bx {{ $index === 0 ? 'bx-user' : ($index === 1 ? 'bx-briefcase' : 'bx-crown') }} fs-2"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- Plan Name -->
                                <h4 class="mb-2">{{ $package->name }}</h4>
                                <p class="text-muted mb-4">{{ $package->description }}</p>

                                <!-- Price -->
                                <div class="mb-4">
                                    <h2 class="text-primary mb-0">
                                        Rp {{ number_format($package->base_price, 0, ',', '.') }}
                                    </h2>
                                    <small class="text-muted">/tahun</small>
                                </div>

                                <!-- Features -->
                                <div class="mb-4">
                                    @if($package->features)
                                        @foreach($package->features as $feature => $value)
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bx bx-check text-success me-2"></i>
                                                <span class="text-muted">
                                                    @if(is_bool($value))
                                                        {{ $value ? ucfirst(str_replace('_', ' ', $feature)) : 'No ' . str_replace('_', ' ', $feature) }}
                                                    @else
                                                        {{ $value }} {{ str_replace('_', ' ', $feature) }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bx bx-check text-success me-2"></i>
                                            <span class="text-muted">Fitur standar sudah termasuk</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Button -->
                                @if($service->product === $package->name)
                                    <button class="btn btn-success w-100" disabled>
                                        Paket Anda Saat Ini
                                    </button>
                                @else
                                    <button class="btn {{ $index === 1 ? 'btn-primary' : 'btn-outline-primary' }} w-100" 
                                            onclick="selectPlan({{ $package->id }}, '{{ $package->name }}', {{ $package->base_price }})">
                                        Upgrade
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upgrade Request Modal -->
<div class="modal fade" id="upgradeRequestModal" tabindex="-1" aria-labelledby="upgradeRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="upgradeRequestModalLabel">
                    <i class="bx bx-up-arrow-alt me-2"></i>Permintaan Upgrade Layanan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="upgradeRequestForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>Proses Permintaan Upgrade:</strong> Permintaan Anda akan ditinjau oleh tim admin kami. Anda akan diberitahu setelah disetujui dan invoice akan dibuat.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="current_plan" class="form-label">Paket Saat Ini</label>
                                <input type="text" class="form-control" id="current_plan" name="current_plan" 
                                       value="{{ $service->product }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="current_price" class="form-label">Harga Saat Ini</label>
                                <input type="text" class="form-control" id="current_price" name="current_price" 
                                       value="Rp {{ number_format($service->price, 0, ',', '.') }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="requested_plan" class="form-label">Paket yang Diminta</label>
                                <input type="text" class="form-control" id="requested_plan" name="requested_plan" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="requested_price" class="form-label">Harga Baru</label>
                                <input type="text" class="form-control" id="requested_price" name="requested_price" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="billing_cycle" class="form-label">Siklus Tagihan</label>
                        <input type="text" class="form-control" id="billing_cycle" name="billing_cycle" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="upgrade_reason" class="form-label">Alasan Upgrade <span class="text-danger">*</span></label>
                        <select class="form-select" id="upgrade_reason" name="upgrade_reason" required>
                            <option value="">Pilih alasan...</option>
                            <option value="need_more_resources">Butuh Lebih Banyak Resource</option>
                            <option value="additional_features">Butuh Fitur Tambahan</option>
                            <option value="business_growth">Pertumbuhan Bisnis</option>
                            <option value="performance_improvement">Peningkatan Performa</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="additional_notes" class="form-label">Catatan Tambahan</label>
                        <textarea class="form-control" id="additional_notes" name="additional_notes" rows="3" 
                                  placeholder="Berikan informasi tambahan tentang permintaan upgrade Anda..."></textarea>
                    </div>

                    <!-- Price Comparison -->
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Perbandingan Harga</h6>
                            <div class="row text-center">
                                <div class="col-4">
                                    <small class="text-muted">Saat Ini</small>
                                    <div class="fw-bold">Rp {{ number_format($service->price, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-4">
                                    <i class="bx bx-right-arrow-alt text-primary"></i>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Baru</small>
                                    <div class="fw-bold text-primary" id="new_price_display">-</div>
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <small class="text-muted">Selisih harga: </small>
                                <span class="fw-bold" id="price_difference">-</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="submitUpgradeBtn" onclick="submitUpgradeRequest()">
                        <i class="bx bx-paper-plane me-2"></i>Kirim Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility() {
    const passwordField = document.getElementById('password-field');
    const toggleIcon = document.getElementById('password-toggle-icon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('bx-show');
        toggleIcon.classList.add('bx-hide');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('bx-hide');
        toggleIcon.classList.add('bx-show');
    }
}

function copyToClipboard(text) {
    if (text) {
        navigator.clipboard.writeText(text).then(function() {
            showToast('Disalin ke clipboard!', 'success');
        }).catch(function() {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showToast('Disalin ke clipboard!', 'success');
        });
    }
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 250px;';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bx bx-check-circle me-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

function loginDashboard(domain) {
    @if($service->login_url)
        window.open('{{ $service->login_url }}', '_blank');
    @elseif($service->domain)
        window.open('https://{{ $service->domain }}/admin', '_blank');
    @else
        alert('URL dashboard tidak tersedia untuk layanan ini');
    @endif
}

function contactSupport() {
    // WhatsApp link or support form
    const message = encodeURIComponent('{{ str_replace('{service_name}', $service->product ?? 'Service', config('company.support_messages.service_issue')) }}');
    window.open(`{{ config('company.whatsapp_url') }}?text=${message}`, '_blank');
}

function changePassword() {
    // Confirm renewal
    if (!confirm('Apakah Anda ingin mengajukan perpanjangan untuk layanan ini?\n\nSetelah diajukan:\n- Invoice akan dibuat\n- Permintaan akan dikirim ke admin\n- Status layanan akan berubah menjadi Pending')) {
        return;
    }
    
    // Show loading
    showToast('Mengajukan permintaan perpanjangan...', 'info');
    
    console.log('Service ID: {{ $service->id }}');
    console.log('Renewal URL: /client/services/{{ $service->id }}/renewal');
    
    // Create renewal invoice
    fetch(`/client/services/{{ $service->id }}/renewal`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.log('Error response text:', text);
                throw new Error(`HTTP ${response.status}: ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            showToast('Permintaan perpanjangan berhasil diajukan!', 'success');
            
            // Show confirmation dialog with payment option
            const confirmPayment = confirm(
                `Permintaan perpanjangan berhasil!\n\n` +
                `Invoice #${data.invoice_number} telah dibuat.\n` +
                `Jumlah: Rp ${Number(data.amount).toLocaleString('id-ID')}\n` +
                `Jatuh Tempo: ${data.due_date}\n\n` +
                `Status layanan Anda sekarang: Pending\n` +
                `Permintaan akan ditinjau oleh admin.\n\n` +
                `Apakah Anda ingin langsung melakukan pembayaran sekarang?`
            );
            
            if (confirmPayment) {
                window.location.href = data.payment_url;
            } else {
                // Reload page to show updated status
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }
        } else {
            showToast(data.message || 'Gagal mengajukan perpanjangan', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan: ' + error.message, 'danger');
    });
}

function upgradePlan() {
    // Show upgrade plan modal
    const modal = new bootstrap.Modal(document.getElementById('upgradePlanModal'));
    modal.show();
}

function cancelSubscription() {
    // Show cancellation reason modal
    const reason = prompt('Berikan alasan pembatalan:');
    if (!reason || reason.trim() === '') {
        showToast('Alasan pembatalan diperlukan.', 'warning');
        return;
    }
    
    // Show loading
    showToast('Memproses permintaan pembatalan...', 'warning');
    
    // Submit cancellation request
    fetch(`/services/{{ $service->id }}/cancellation-request`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            cancellation_reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Permintaan pembatalan berhasil dikirim! Tim admin kami akan segera meninjaunya.', 'success');
            
            // Optionally refresh the page after a delay
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showToast(data.message || 'Gagal mengirim permintaan pembatalan', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat mengirim permintaan Anda', 'danger');
    });
}

// Billing is now fixed to annually only

function selectPlan(packageId, packageName, price) {
    // Close pricing modal and open upgrade request modal
    const pricingModal = bootstrap.Modal.getInstance(document.getElementById('upgradePlanModal'));
    pricingModal.hide();
    
    // Fill upgrade request form
    document.getElementById('requested_plan').value = packageName;
    document.getElementById('requested_price').value = price;
    
    // Set billing cycle to annually (fixed)
    document.getElementById('billing_cycle').value = 'annually';
    
    // Update price comparison (use base price directly)
    const currentPrice = {{ $service->price }};
    const newPrice = price;
    const priceDifference = newPrice - currentPrice;
    
    document.getElementById('new_price_display').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(newPrice);
    
    const diffElement = document.getElementById('price_difference');
    const sign = priceDifference >= 0 ? '+' : '';
    diffElement.textContent = sign + 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(priceDifference));
    diffElement.className = 'fw-bold ' + (priceDifference >= 0 ? 'text-success' : 'text-danger');
    
    // Show upgrade request modal
    const upgradeRequestModal = new bootstrap.Modal(document.getElementById('upgradeRequestModal'));
    upgradeRequestModal.show();
}

function submitUpgradeRequest() {
    const form = document.getElementById('upgradeRequestForm');
    const formData = new FormData(form);
    
    // Show loading
    const submitBtn = document.getElementById('submitUpgradeBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
    submitBtn.disabled = true;
    
    fetch(`/services/{{ $service->id }}/upgrade-request`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast('Permintaan upgrade berhasil dikirim!', 'success');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('upgradeRequestModal'));
            modal.hide();
            
            // Reset form
            form.reset();
            
            // Show success message
            setTimeout(() => {
                alert('Permintaan upgrade Anda telah dikirim dan menunggu persetujuan admin. Anda akan diberitahu setelah diproses.');
            }, 1000);
        } else {
            showToast(data.message || 'Gagal mengirim permintaan upgrade', 'danger');
            console.error('Server error:', data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat mengirim permintaan Anda: ' + error.message, 'danger');
    })
    .finally(() => {
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Auto-refresh upgrade request status every 30 seconds
function refreshUpgradeStatus() {
    fetch(`/services/{{ $service->id }}/upgrade-status`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.hasUpgradeRequest && data.status !== 'pending') {
            // Status changed, reload page to show updated status
            showToast('Status permintaan upgrade Anda telah diperbarui!', 'info');
            setTimeout(() => location.reload(), 2000);
        }
    })
    .catch(error => {
        console.log('Error checking upgrade status:', error);
    });
}

// Start auto-refresh if there's a pending request
@php
    $hasPendingRequest = \App\Models\ServiceUpgradeRequest::where('service_id', $service->id)
        ->where('client_id', auth()->id())
        ->where('status', 'pending')
        ->exists();
@endphp

@if($hasPendingRequest)
    setInterval(refreshUpgradeStatus, 30000); // Check every 30 seconds
@endif
</script>
@endsection
