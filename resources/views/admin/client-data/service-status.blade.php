@extends('layouts.sneat-dashboard')

@section('title', 'Service Status Overview')

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
            <li class="breadcrumb-item active">Service Status Overview</li>
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
                                <i class="bx bx-bar-chart me-2"></i>Service Status Overview
                            </h5>
                            <p class="text-muted mb-0">Monitor all services expiration and status</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.client-data.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Back to Client Data
                            </a>
                            <button onclick="exportData()" class="btn btn-success">
                                <i class="bx bx-download me-1"></i>Export Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success bg-lighten">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-label-success me-3">
                            <i class="bx bx-check-circle fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $overview['total_clients'] }}</h4>
                            <small class="text-muted">Total Clients</small>
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
                            <h4 class="mb-0">{{ $overview['expiring_soon'] }}</h4>
                            <small class="text-muted">Expiring Soon (30 days)</small>
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
                            <h4 class="mb-0">{{ $overview['expired_services'] }}</h4>
                            <small class="text-muted">Expired Services</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info bg-lighten">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-label-info me-3">
                            <i class="bx bx-server fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $overview['servers_in_use'] }}</h4>
                            <small class="text-muted">Servers in Use</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Server Statistics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-server me-2"></i>Server Statistics
                    </h5>
                </div>
                <div class="card-body">
                    @if($serverStats->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Server Name</th>
                                        <th>IP Address</th>
                                        <th>Total Clients</th>
                                        <th>Expiring Soon</th>
                                        <th>Expired</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serverStats as $stat)
                                        @php
                                            $server = \App\Models\Server::find($stat['server_id']);
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $stat['server_name'] }}</strong>
                                            </td>
                                            <td>
                                                <code>{{ $server->ip_address ?? 'N/A' }}</code>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $stat['client_count'] }}</span>
                                            </td>
                                            <td>
                                                @if($stat['expiring_soon'] > 0)
                                                    <span class="badge bg-warning">{{ $stat['expiring_soon'] }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($stat['expired'] > 0)
                                                    <span class="badge bg-danger">{{ $stat['expired'] }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $server->status_badge_class }}">
                                                    {{ ucfirst($server->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">No servers with clients found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Domain Register Statistics -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-globe me-2"></i>Domain Register Statistics
                    </h5>
                </div>
                <div class="card-body">
                    @if($registerStats->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Register Name</th>
                                        <th>Total Clients</th>
                                        <th>Expiring Soon</th>
                                        <th>Expired</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($registerStats as $stat)
                                        @php
                                            $register = \App\Models\DomainRegister::find($stat['register_id']);
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $stat['register_name'] }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $stat['client_count'] }}</span>
                                            </td>
                                            <td>
                                                @if($stat['expiring_soon'] > 0)
                                                    <span class="badge bg-warning">{{ $stat['expiring_soon'] }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($stat['expired'] > 0)
                                                    <span class="badge bg-danger">{{ $stat['expired'] }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $register->status_badge_class }}">
                                                    {{ ucfirst($register->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ $register->login_link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bx bx-link"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">No domain registers with clients found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Expirations -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bx bx-time-five me-2"></i>Upcoming Expirations (Next 30 Days)
            </h5>
        </div>
        <div class="card-body">
            @php
                $upcomingExpirations = \App\Models\ClientData::with(['server', 'domainRegister'])
                    ->where(function($q) {
                        $q->where('website_service_expired', '<=', now()->addDays(30))
                          ->orWhere('domain_expired', '<=', now()->addDays(30))
                          ->orWhere('hosting_expired', '<=', now()->addDays(30));
                    })
                    ->orderByRaw('
                        CASE 
                            WHEN website_service_expired <= CURDATE() THEN website_service_expired
                            WHEN domain_expired <= CURDATE() THEN domain_expired
                            WHEN hosting_expired <= CURDATE() THEN hosting_expired
                            ELSE LEAST(website_service_expired, domain_expired, hosting_expired)
                        END
                    ')
                    ->get();
            @endphp
            
            @if($upcomingExpirations->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Client Name</th>
                                <th>WhatsApp</th>
                                <th>Server</th>
                                <th>Service Type</th>
                                <th>Expiration Date</th>
                                <th>Days Remaining</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingExpirations as $client)
                                @php
                                    $expirations = [];
                                    if ($client->website_service_expired && $client->website_service_expired->lte(now()->addDays(30))) {
                                        $expirations[] = ['type' => 'Website Service', 'date' => $client->website_service_expired];
                                    }
                                    if ($client->domain_expired && $client->domain_expired->lte(now()->addDays(30))) {
                                        $expirations[] = ['type' => 'Domain', 'date' => $client->domain_expired];
                                    }
                                    if ($client->hosting_expired && $client->hosting_expired->lte(now()->addDays(30))) {
                                        $expirations[] = ['type' => 'Hosting', 'date' => $client->hosting_expired];
                                    }
                                @endphp
                                
                                @foreach($expirations as $expiration)
                                    <tr>
                                        <td>
                                            <strong>{{ $client->name }}</strong>
                                            @if($loop->first)
                                                <br>
                                                <small class="text-muted">{{ Str::limit($client->address, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($loop->first)
                                                <a href="{{ $client->whatsapp_link }}" target="_blank" class="text-decoration-none">
                                                    <i class="bx bxl-whatsapp text-success me-1"></i>
                                                    {{ $client->whatsapp }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($loop->first && $client->server)
                                                <div>
                                                    <strong>{{ $client->server->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $client->server->ip_address }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-label-primary">{{ $expiration['type'] }}</span>
                                        </td>
                                        <td>
                                            <span class="{{ $expiration['date']->isPast() ? 'text-danger' : ($expiration['date']->lte(now()->addDays(7)) ? 'text-warning' : 'text-info') }}">
                                                {{ $expiration['date']->format('M d, Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $daysRemaining = $expiration['date']->diffInDays(now(), false);
                                            @endphp
                                            @if($daysRemaining < 0)
                                                <span class="badge bg-danger">Expired {{ abs($daysRemaining) }} days ago</span>
                                            @elseif($daysRemaining <= 7)
                                                <span class="badge bg-danger">{{ $daysRemaining }} days</span>
                                            @elseif($daysRemaining <= 30)
                                                <span class="badge bg-warning">{{ $daysRemaining }} days</span>
                                            @else
                                                <span class="badge bg-info">{{ $daysRemaining }} days</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($expiration['date']->isPast())
                                                <span class="badge bg-danger">Expired</span>
                                            @elseif($expiration['date']->lte(now()->addDays(7)))
                                                <span class="badge bg-warning">Critical</span>
                                            @else
                                                <span class="badge bg-info">Warning</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($loop->first)
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-horizontal"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.client-data.edit', $client) }}">
                                                                <i class="bx bx-edit me-2"></i>Edit Client
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ $client->whatsapp_link }}" target="_blank">
                                                                <i class="bx bxl-whatsapp me-2"></i>WhatsApp
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="mailto:client@example.com">
                                                                <i class="bx bx-envelope me-2"></i>Send Email
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="avatar avatar-xl mx-auto mb-3">
                        <span class="avatar-initial rounded-circle bg-label-success">
                            <i class="bx bx-check-circle fs-2"></i>
                        </span>
                    </div>
                    <h5>No Upcoming Expirations</h5>
                    <p class="text-muted mb-4">All services are in good standing!</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Export data function
function exportData() {
    window.location.href = '{{ route("admin.client-data.export") }}';
}

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
