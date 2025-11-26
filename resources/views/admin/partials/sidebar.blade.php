<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <!-- Brand -->
    <div class="app-brand demo">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('images/exputra-logo.png') }}" alt="Exputra" style="height: 25px;">
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">Exputra Billing</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        <!-- Clients -->
        <li class="menu-item {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
            <a href="{{ route('admin.clients.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Clients">Clients</div>
            </a>
        </li>

        <!-- Services -->
        <li class="menu-item {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
            <a href="{{ route('admin.services.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div data-i18n="Services">Services</div>
            </a>
        </li>

        <!-- Service Packages -->
        <li class="menu-item {{ request()->routeIs('admin.service-packages.*') ? 'active' : '' }}">
            <a href="{{ route('admin.service-packages.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-box"></i>
                <div data-i18n="Service Packages">Service Packages</div>
            </a>
        </li>

        <!-- Domain Extensions -->
        <li class="menu-item {{ request()->routeIs('admin.domain-extensions.*') ? 'active' : '' }}">
            <a href="{{ route('admin.domain-extensions.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-globe"></i>
                <div data-i18n="Domain Extensions">Domain Extensions</div>
            </a>
        </li>

        <!-- Invoices -->
        <li class="menu-item {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
            <a href="{{ route('admin.invoices.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-receipt"></i>
                <div data-i18n="Invoices">Invoices</div>
            </a>
        </li>

        <!-- Upgrade Requests -->
        @if(Route::has('admin.upgrade-requests.index'))
        <li class="menu-item {{ request()->routeIs('admin.upgrade-requests.*') ? 'active' : '' }}">
            <a href="{{ route('admin.upgrade-requests.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-credit-card"></i>
                <div data-i18n="Upgrade Requests">Upgrade Requests</div>
                @php
                    $pendingCount = \App\Models\ServiceUpgradeRequest::where('status', 'pending')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="badge badge-center rounded-pill bg-danger ms-auto">{{ $pendingCount }}</span>
                @endif
            </a>
        </li>
        @endif

        <!-- Server Management -->
        <li class="menu-item {{ request()->routeIs('admin.servers.*') ? 'active' : '' }}">
            <a href="{{ route('admin.servers.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-server"></i>
                <div data-i18n="Server Management">Server Management</div>
                @php
                    $serverCount = \App\Models\Server::count();
                @endphp
                <span class="badge badge-center rounded-pill bg-primary ms-auto">{{ $serverCount }}</span>
            </a>
        </li>

        <!-- Domain Register Management -->
        <li class="menu-item {{ request()->routeIs('admin.domain-registers.*') ? 'active' : '' }}">
            <a href="{{ route('admin.domain-registers.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-globe"></i>
                <div data-i18n="Domain Register">Domain Register</div>
                @php
                    $registerCount = \App\Models\DomainRegister::count();
                @endphp
                <span class="badge badge-center rounded-pill bg-info ms-auto">{{ $registerCount }}</span>
            </a>
        </li>

        <!-- Client Data Management -->
        <li class="menu-item {{ request()->routeIs('admin.client-data.*') ? 'active' : '' }}">
            <a href="{{ route('admin.client-data.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-voice"></i>
                <div data-i18n="Client Data">Client Data</div>
            </a>
        </li>

        
        <!-- Domains -->
        <li class="menu-item {{ request()->routeIs('admin.domains.*') ? 'active' : '' }}">
            <a href="{{ route('admin.domains.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-link"></i>
                <div data-i18n="Domains">Domains</div>
                @php
                    // Use MySQL queries for better performance
                    $expiringCount = \Illuminate\Support\Facades\DB::select('SELECT COUNT(*) as count FROM domains WHERE expired_date >= ? AND expired_date <= ?', [now(), now()->addDays(30)])[0]->count;
                    $expiredCount = \Illuminate\Support\Facades\DB::select('SELECT COUNT(*) as count FROM domains WHERE expired_date < ?', [now()])[0]->count;
                    $totalAlerts = $expiringCount + $expiredCount;
                @endphp
                @if($totalAlerts > 0)
                    <span class="badge {{ $expiredCount > 0 ? 'bg-danger' : 'bg-warning' }} rounded-pill ms-auto">{{ $totalAlerts }}</span>
                @endif
            </a>
        </li>

        <!-- Settings Header -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Settings</span>
        </li>

        <!-- Gmail Settings -->
        @if(Route::has('admin.settings.gmail'))
        <li class="menu-item {{ request()->routeIs('admin.settings.gmail*') ? 'active' : '' }}">
            <a href="{{ route('admin.settings.gmail') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-envelope"></i>
                <div data-i18n="Gmail API">Gmail API</div>
                @php
                    $gmailService = app(\App\Services\GmailService::class);
                    $isGmailAuth = $gmailService->isAuthenticated();
                @endphp
                @if(!$isGmailAuth)
                    <span class="badge bg-danger rounded-pill ms-auto">!</span>
                @endif
            </a>
        </li>
        @endif

    </ul>
</aside>
