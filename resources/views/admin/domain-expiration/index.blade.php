@extends('layouts.sneat-dashboard')

@section('title', 'Domain Expiration Monitoring')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Domain Expiration</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="bx bx-time-five me-2"></i>Domain Expiration Monitoring
                        </h5>
                        <small class="text-muted">Monitor domain expiration for all clients</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.domain-expiration.guide') }}" class="btn btn-outline-info btn-sm">
                            <i class="bx bx-book-reader me-1"></i>Guide
                        </a>
                        <form method="GET" class="d-flex gap-2">
                            <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>All Domains</option>
                                <option value="expired" {{ $filter == 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="expiring" {{ $filter == 'expiring' ? 'selected' : '' }}>Expiring (3 Months)</option>
                                <option value="safe" {{ $filter == 'safe' ? 'selected' : '' }}>Safe</option>
                            </select>
                        </form>
                        <form method="POST" action="{{ route('admin.domain-expiration.export') }}">
                            @csrf
                            <input type="hidden" name="filter" value="{{ $filter }}">
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-download me-1"></i>Export
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h6 class="mb-2">Total Domains</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-label-primary rounded-circle">
                            <i class="bx bx-globe fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h6 class="mb-2 text-danger">Expired</h6>
                            <h3 class="mb-0 text-danger">{{ $stats['expired'] }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-label-danger rounded-circle">
                            <i class="bx bx-x-circle fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h6 class="mb-2 text-warning">Expiring Soon</h6>
                            <h3 class="mb-0 text-warning">{{ $stats['expiring'] }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-label-warning rounded-circle">
                            <i class="bx bx-time-five fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h6 class="mb-2 text-success">Safe</h6>
                            <h3 class="mb-0 text-success">{{ $stats['safe'] }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-label-success rounded-circle">
                            <i class="bx bx-check-circle fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Sections -->
    @if($criticalExpirations->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">
                        <i class="bx bx-error me-2"></i>Critical: Domains Expiring in 7 Days
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Domain Register</th>
                                    <th>Expired Date</th>
                                    <th>Days Left</th>
                                    <th>WhatsApp</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criticalExpirations as $client)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ substr($client->name, 0, 1) }}
                                                </span>
                                            </div>
                                            {{ $client->name }}
                                        </div>
                                    </td>
                                    <td>{{ $client->domainRegister->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-danger">{{ $client->domain_expired->format('M d, Y') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">{{ $client->domain_expired->diffInDays(now()) }} days</span>
                                    </td>
                                    <td>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $client->whatsapp) }}" 
                                           target="_blank" class="btn btn-sm btn-outline-success">
                                            <i class="bx bx-message-rounded-detail"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.domain-expiration.send-reminder', $client) }}" target="_blank">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning">
                                                <i class="bx bx-bell me-1"></i>Send Reminder
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        Client Domains 
                        @if($filter != 'all')
                            <span class="badge bg-secondary ms-2">{{ ucfirst($filter) }}</span>
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    @if($clientDomains->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Domain Register</th>
                                        <th>Domain Expired</th>
                                        <th>Website Expired</th>
                                        <th>Hosting Expired</th>
                                        <th>Status</th>
                                        <th>WhatsApp</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clientDomains as $client)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ substr($client->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $client->name }}</div>
                                                    <small class="text-muted">{{ $client->address }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($client->domainRegister)
                                                <div>{{ $client->domainRegister->name }}</div>
                                                <small class="text-muted">{{ $client->domainRegister->login_link }}</small>
                                            @else
                                                <span class="text-muted">
                                                    <i class="bx bx-info-circle"></i> No domain assigned
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge {{ $client->domain_expired->isPast() ? 'bg-danger' : ($client->domain_expired->lte(now()->addMonths(3)) ? 'bg-warning' : 'bg-success') }}">
                                                    {{ $client->domain_expired->format('M d, Y') }}
                                                </span>
                                                <small class="text-muted">
                                                    {{ $client->domain_expired->diffInDays(now(), false) }} days
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $client->website_service_expired->isPast() ? 'bg-danger' : ($client->website_service_expired->lte(now()->addMonths(3)) ? 'bg-warning' : 'bg-success') }}">
                                                {{ $client->website_service_expired->format('M d, Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $client->hosting_expired->isPast() ? 'bg-danger' : ($client->hosting_expired->lte(now()->addMonths(3)) ? 'bg-warning' : 'bg-success') }}">
                                                {{ $client->hosting_expired->format('M d, Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $client->status == 'active' ? 'bg-success' : ($client->status == 'warning' ? 'bg-warning' : 'bg-danger') }}">
                                                {{ ucfirst($client->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $client->whatsapp) }}" 
                                               target="_blank" class="btn btn-sm btn-outline-success">
                                                <i class="bx bx-message-rounded-detail"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-horizontal-rounded"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.domain-expiration.send-reminder', $client) }}" target="_blank">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="bx bx-bell me-2"></i>Send Reminder
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('admin.client-data.edit', $client) }}" class="dropdown-item">
                                                            <i class="bx bx-edit me-2"></i>Edit Client
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('admin.client-data.show', $client) }}" class="dropdown-item">
                                                            <i class="bx bx-show me-2"></i>View Details
                                                        </a>
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
                            <h5>No Client Data Found</h5>
                            <p class="text-muted mb-4">
                                @if($filter == 'expired')
                                    No expired domains found. Great job!
                                @elseif($filter == 'expiring')
                                    No domains expiring in the next 3 months.
                                @elseif($filter == 'safe')
                                    All domains need attention within 3 months.
                                @else
                                    No client data found. You need to add client data first to monitor domain expiration.
                                @endif
                            </p>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('admin.client-data.create') }}" class="btn btn-primary">
                                    <i class="bx bx-plus me-1"></i>Add Client Data
                                </a>
                                @if($stats['total'] == 0)
                                    <button type="button" class="btn btn-outline-secondary" onclick="loadSampleData()">
                                        <i class="bx bx-data me-1"></i>Load Sample Data
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
function loadSampleData() {
    if(confirm('Are you sure you want to load sample client data? This will create 6 sample clients with various expiration scenarios.')) {
        fetch('/admin/domain-expiration/load-sample-data', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Sample data loaded successfully! Refreshing the page...');
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading sample data');
        });
    }
}
</script>
