<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand">
        <a href="{{ route('client.dashboard') }}" class="app-brand-link">
            <span class="app-brand-text h5">Client Portal</span>
        </a>
    </div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
            <a href="{{ route('client.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- My Services -->
        <li class="menu-item {{ request()->routeIs('client.services.*') ? 'active' : '' }}">
            <a href="/client/services" class="menu-link">
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
                <i class="menu-icon tf-icons bx bx-up-arrow-alt"></i>
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
