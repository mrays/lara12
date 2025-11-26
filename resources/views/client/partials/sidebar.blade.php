<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('client.dashboard') }}" class="app-brand-link">
            <img src="{{ asset('images/exputra-logo.png') }}" alt="Exputra" style="height: 25px;">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ config('company.name', 'Exputra') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>
    
    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
            <a href="{{ route('client.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Order New Service -->
        <li class="menu-item {{ request()->routeIs('client.orders.*') ? 'active' : '' }}">
            <a href="{{ route('client.orders.create') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cart-add"></i>
                <span>Order Service</span>
            </a>
        </li>

        <!-- My Services -->
        <li class="menu-item {{ request()->routeIs('client.services.*') ? 'active' : '' }}">
            <a href="{{ route('client.services.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <span>My Services</span>
            </a>
        </li>

        <!-- Invoices -->
        <li class="menu-item {{ request()->routeIs('client.invoices.*') ? 'active' : '' }}">
            <a href="{{ route('client.invoices.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-receipt"></i>
                <span>Invoices</span>
            </a>
        </li>

        <!-- Upgrade Requests -->
        <li class="menu-item {{ request()->routeIs('client.upgrade-requests.*') ? 'active' : '' }}">
            <a href="{{ route('client.upgrade-requests.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-credit-card"></i>
                <span>Upgrade Requests</span>
            </a>
        </li>

        <!-- Profile -->
        <li class="menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <a href="{{ route('profile.edit') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <span>Profile</span>
            </a>
        </li>
    </ul>
</aside>
