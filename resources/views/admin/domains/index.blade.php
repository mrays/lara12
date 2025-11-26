@extends('layouts.sneat-dashboard')

@section('title', 'Domain Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Domain Management</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="bx bx-link me-2"></i>Domain Management
                        </h5>
                        <small class="text-muted">Manage individual domains with client and server assignments</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.domains.create') }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i>Add Domain
                        </a>
                        <form method="GET" class="d-flex gap-2">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Search domain..." value="{{ request('search') }}">
                            </div>
                            <select name="filter" class="form-select form-select-sm" style="width: auto;">
                                <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>All Domains</option>
                                <option value="active" {{ $filter == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="expired" {{ $filter == 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="expiring" {{ $filter == 'expiring' ? 'selected' : '' }}>Expiring (30 Days)</option>
                                <option value="critical" {{ $filter == 'critical' ? 'selected' : '' }}>Critical (7 Days)</option>
                                <option value="safe" {{ $filter == 'safe' ? 'selected' : '' }}>Safe</option>
                                <option value="pending" {{ $filter == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="suspended" {{ $filter == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bx bx-filter me-1"></i>Filter
                            </button>
                            @if(request('search') || request('filter') != 'all')
                            <a href="{{ route('admin.domains.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-reset me-1"></i>Reset
                            </a>
                            @endif
                        </form>
                        <form method="POST" action="{{ route('admin.domains.export') }}">
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
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h6 class="mb-2">Total</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-label-primary rounded-circle">
                            <i class="bx bx-link fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h6 class="mb-2 text-success">Active</h6>
                            <h3 class="mb-0 text-success">{{ $stats['active'] }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-label-success rounded-circle">
                            <i class="bx bx-check-circle fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 mb-4">
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
        
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h6 class="mb-2 text-warning">Expiring</h6>
                            <h3 class="mb-0 text-warning">{{ $stats['expiring'] }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-label-warning rounded-circle">
                            <i class="bx bx-time-five fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h6 class="mb-2 text-danger">Critical</h6>
                            <h3 class="mb-0 text-danger">{{ $stats['critical'] }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-label-danger rounded-circle">
                            <i class="bx bx-error fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h6 class="mb-2 text-info">Pending</h6>
                            <h3 class="mb-0 text-info">{{ $stats['pending'] }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-label-info rounded-circle">
                            <i class="bx bx-time fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Critical Alerts -->
    @if(count($criticalExpirations) > 0)
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
                                    <th>Domain</th>
                                    <th>Client</th>
                                    <th>Server</th>
                                    <th>Register</th>
                                    <th>Expired Date</th>
                                    <th>Days Left</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criticalExpirations as $domain)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ substr($domain->domain_name, 0, 1) }}
                                                </span>
                                            </div>
                                            <strong>{{ $domain->domain_name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        @if($domain->client_name)
                                            <span class="badge bg-info">{{ $domain->client_name }}</span>
                                        @else
                                            <span class="text-muted">No client</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($domain->server_name)
                                            <span class="badge bg-secondary">{{ $domain->server_name }}</span>
                                        @else
                                            <span class="text-muted">No server</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($domain->domain_register_name)
                                            <span class="badge bg-warning">{{ $domain->domain_register_name }}</span>
                                        @else
                                            <span class="text-muted">No register</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">{{ $domain->expired_date->format('M d, Y') }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $daysLeft = $domain->expired_date->diffInDays(now(), false);
                                            $isExpired = $domain->expired_date->isPast();
                                            $daysRounded = abs(round($daysLeft));
                                        @endphp
                                        <span class="badge bg-danger">
                                            @if($isExpired)
                                                -{{ $daysRounded }} hari
                                            @else
                                                {{ $daysRounded }} hari lagi
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.domains.send-reminder', $domain->id) }}" target="_blank">
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
                        All Domains 
                        @if($filter != 'all')
                            <span class="badge bg-secondary ms-2">{{ ucfirst($filter) }}</span>
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    @if(count($domains) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Domain</th>
                                        <th>Client</th>
                                        <th>Server</th>
                                        <th>Register</th>
                                        <th>Expired Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($domains as $domain)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ substr($domain->domain_name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $domain->domain_name }}</div>
                                                    <small class="text-muted">{{ $domain->notes }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($domain->client_name)
                                                <span class="badge bg-info">{{ $domain->client_name }}</span>
                                            @else
                                                <span class="text-muted">No client</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($domain->server_name)
                                                <span class="badge bg-secondary">{{ $domain->server_name }}</span>
                                            @else
                                                <span class="text-muted">No server</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($domain->domain_register_name)
                                                <span class="badge bg-warning">{{ $domain->domain_register_name }}</span>
                                            @else
                                                <span class="text-muted">No register</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($domain->expired_date)
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge {{ $domain->expired_date->isPast() ? 'bg-danger' : ($domain->expired_date->lte(now()->addDays(30)) ? 'bg-warning' : 'bg-success') }}">
                                                        {{ $domain->expired_date->format('M d, Y') }}
                                                    </span>
                                                    @php
                                                        $daysLeft = $domain->expired_date->diffInDays(now(), false);
                                                        $isExpired = $domain->expired_date->isPast();
                                                        $daysRounded = abs(round($daysLeft));
                                                    @endphp
                                                    <small class="text-muted">
                                                        @if($isExpired)
                                                            -{{ $daysRounded }} hari
                                                        @else
                                                            {{ $daysRounded }} hari lagi
                                                        @endif
                                                    </small>
                                                </div>
                                            @else
                                                <span class="badge bg-secondary">Not Set</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $domain->status == 'active' ? 'success' : ($domain->status == 'expired' ? 'danger' : ($domain->status == 'pending' ? 'warning' : 'secondary')) }}">
                                                {{ ucfirst($domain->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-horizontal-rounded"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="{{ route('admin.domains.edit', $domain->id) }}" class="dropdown-item">
                                                            <i class="bx bx-edit me-2"></i>Edit Domain
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.domains.send-reminder', $domain->id) }}" target="_blank">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="bx bx-bell me-2"></i>Send Reminder
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.domains.destroy', $domain->id) }}" onsubmit="return confirm('Are you sure you want to delete this domain?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="bx bx-trash me-2"></i>Delete Domain
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
                                    <i class="bx bx-link fs-2"></i>
                                </span>
                            </div>
                            <h5>No Domains Found</h5>
                            <p class="text-muted mb-4">
                                @if($filter == 'expired')
                                    No expired domains found. Great job!
                                @elseif($filter == 'expiring')
                                    No domains expiring in the next 30 days.
                                @elseif($filter == 'critical')
                                    No critical expirations in the next 7 days.
                                @elseif($filter == 'safe')
                                    No domains with more than 30 days until expiration.
                                @elseif($filter == 'active')
                                    No active domains found.
                                @elseif($filter == 'pending')
                                    No pending domains found.
                                @elseif($filter == 'suspended')
                                    No suspended domains found.
                                @else
                                    No domains found. You need to add domains first to manage them.
                                @endif
                            </p>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('admin.domains.create') }}" class="btn btn-primary">
                                    <i class="bx bx-plus me-1"></i>Add Domain
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
