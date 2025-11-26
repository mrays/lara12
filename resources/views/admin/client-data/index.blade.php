@extends('layouts.sneat-dashboard')

@section('title', 'Client Data Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Client Data</li>
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
                                <i class="bx bx-user me-2"></i>Client Data Management
                            </h5>
                            <p class="text-muted mb-0">Kelola data client dan informasi layanan</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.client-data.service-status') }}" class="btn btn-info">
                                <i class="bx bx-bar-chart me-1"></i>Service Status
                            </a>
                            <a href="{{ route('admin.client-data.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i>Tambah Client
                            </a>
                        </div>
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
            <div class="card bg-warning bg-lighten">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-label-warning me-3">
                            <i class="bx bx-time fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $statusCounts['warning'] }}</h4>
                            <small class="text-muted">Warning</small>
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
            <div class="card bg-primary bg-lighten">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-label-primary me-3">
                            <i class="bx bx-user fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $statusCounts['all'] }}</h4>
                            <small class="text-muted">Total Client</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.client-data.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="warning" {{ request('status') == 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Cari nama client, alamat, whatsapp..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('admin.client-data.index') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Clients List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Daftar Client</h5>
        </div>
        <div class="card-body">
            @if($clients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Client</th>
                                <th>Alamat</th>
                                <th>WhatsApp</th>
                                <th>Domains</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clients as $client)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $client->name }}</strong>
                                            @if($client->user)
                                                <br>
                                                <small class="text-muted">User: {{ $client->user->name }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($client->address, 50) }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="{{ $client->whatsapp_link }}" target="_blank" class="text-decoration-none">
                                                <i class="bx bxl-whatsapp text-success me-1"></i>
                                                {{ $client->whatsapp }}
                                            </a>
                                            <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $client->whatsapp }}')" title="Copy">
                                                <i class="bx bx-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        @if($client->domains->count() > 0)
                                            <div class="small">
                                                @foreach($client->domains->take(3) as $domain)
                                                    <div class="mb-1">
                                                        <strong>{{ $domain->domain_name }}</strong>
                                                        @if($domain->expired_date)
                                                            <span class="{{ $domain->expired_date->isPast() ? 'text-danger' : ($domain->expired_date->lte(now()->addDays(30)) ? 'text-warning' : 'text-success') }}">
                                                                ({{ $domain->expired_date->format('M d, Y') }})
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                                @if($client->domains->count() > 3)
                                                    <small class="text-muted">+{{ $client->domains->count() - 3 }} domain lainnya</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Belum ada domain</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $client->status_badge_class }}">
                                            {{ ucfirst($client->status) }}
                                        </span>
                                        @if($client->isAnyServiceExpired())
                                            <span class="badge bg-danger ms-1">Expired</span>
                                        @elseif($client->isAnyServiceExpiringSoon())
                                            <span class="badge bg-warning ms-1">Expiring</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-horizontal"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.client-data.edit', $client) }}">
                                                        <i class="bx bx-edit me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ $client->whatsapp_link }}" target="_blank">
                                                        <i class="bx bxl-whatsapp me-2"></i>WhatsApp
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.client-data.destroy', $client) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data client ini?')">
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
                            <i class="bx bx-user fs-2"></i>
                        </span>
                    </div>
                    <h5>Belum ada data client</h5>
                    <p class="text-muted mb-4">Tambahkan data client pertama untuk memulai</p>
                    <a href="{{ route('admin.client-data.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>Tambah Client
                    </a>
                </div>
            @endif
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
