<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('vendor/sneat/assets/') }}" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/exputra-logo.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/sneat/assets/vendor/fonts/boxicons.css') }}" />
    <!-- Boxicons CDN fallback -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/sneat/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('vendor/sneat/assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('vendor/sneat/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    @stack('styles')
    
    <!-- Custom Mobile Styles -->
    <style>
        /* Mobile Responsive Improvements */
        @media (max-width: 767.98px) {
            .app-brand-text {
                font-size: 1.1rem !important;
            }
            
            .menu-inner .menu-item .menu-link {
                padding: 0.625rem 1rem !important;
                font-size: 0.9rem !important;
            }
            .badge {
                font-size: 0.7rem !important;
                min-width: 1.2rem !important;
                height: 1.2rem !important;
            }
            
            .layout-menu {
                width: 260px !important;
            }
            
            .layout-menu.menu-collapsed {
                width: 78px !important;
            }
        }
        
        /* Admin vs Client Visual Distinction */
        @if(auth()->check() && auth()->user()->role === 'admin')
        .layout-wrapper {
            --bs-primary: #696cff;
            --bs-primary-rgb: 105, 108, 255;
        }
        .layout-navbar {
            border-bottom: 3px solid #696cff !important;
        }
        .app-brand-text {
            color: #696cff !important;
        }
        @else
        .layout-wrapper {
            --bs-primary: #00bcd4;
            --bs-primary-rgb: 0, 188, 212;
        }
        .layout-navbar {
            border-bottom: 3px solid #00bcd4 !important;
        }
        .app-brand-text {
            color: #00bcd4 !important;
        }
        @endif
        .layout-menu-toggle {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .layout-menu-toggle:hover {
            background-color: rgba(67, 89, 113, 0.1);
            border-radius: 6px;
        }
        
        /* Better badge positioning */
        .menu-link .badge {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .menu-link {
            position: relative;
            padding-right: 3rem !important;
        }
        
        /* Improved mobile menu */
        @media (max-width: 1199.98px) {
            .layout-menu-toggle.menu-link-toggle {
                display: none !important;
            }
        }
        
        /* Management Panel Dropdown Styling */
        .dropdown-management .dropdown-menu {
            border: 1px solid rgba(67, 89, 113, 0.15);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
        }
        
        .dropdown-management .dropdown-header {
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.5rem 1rem;
        }
        
        .dropdown-management .dropdown-item {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }
        
        .dropdown-management .dropdown-item:hover {
            background-color: rgba(67, 89, 113, 0.06);
            transform: translateX(2px);
        }
        
        .dropdown-management .badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }
        
        .dropdown-management .nav-link {
            position: relative;
            transition: all 0.2s ease;
        }
        
        .dropdown-management .nav-link:hover {
            transform: scale(1.05);
        }
        
        .dropdown-management .badge.rounded-pill {
            min-width: 1.5rem;
            height: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            padding: 0;
        }
        
        /* Alert animations */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .dropdown-management .badge.bg-danger {
            animation: pulse 2s infinite;
        }
    </style>

    <!-- Helpers -->
    <script src="{{ asset('vendor/sneat/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('vendor/sneat/assets/js/config.js') }}"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @if(auth()->check() && auth()->user()->role === 'admin')
                @include('admin.partials.sidebar')
            @else
                @include('client.partials.sidebar')
            @endif
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <i class="bx bx-search fs-4 lh-0"></i>
                                <input type="text" class="form-control border-0 shadow-none" placeholder="Search..." aria-label="Search..." />
                            </div>
                        </div>

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Role Indicator -->
                            @if(auth()->check())
                            <li class="nav-item d-none d-md-block">
                                <span class="navbar-text me-3">
                                    <span class="badge {{ auth()->user()->role === 'admin' ? 'bg-danger' : 'bg-info' }} rounded-pill">
                                        <i class="bx {{ auth()->user()->role === 'admin' ? 'bx-shield' : 'bx-user' }} me-1"></i>
                                        {{ ucfirst(auth()->user()->role) }}
                                    </span>
                                </span>
                            </li>
                            
                            <!-- Management Panel -->
                            @if(auth()->user()->role === 'admin')
                            <li class="nav-item navbar-dropdown dropdown-management dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" title="Management Panel">
                                    <i class="bx bx-cog fs-4 lh-0"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        @php
                                            // Count clients with expiring domains (within 30 days)
                                            $expiringCount = \App\Models\ClientData::whereHas('domains', function($q) {
                                                $q->where('expired_date', '<=', now()->addDays(30))
                                                  ->where('expired_date', '>=', now());
                                            })->count();
                                            
                                            $serverIssues = \App\Models\Server::where('status', 'expired')->orWhere('expired_date', '<=', now()->addDays(7))->count();
                                            $registerIssues = \App\Models\DomainRegister::where('status', 'expired')->orWhere('expired_date', '<=', now()->addDays(7))->count();
                                            $totalAlerts = $expiringCount + $serverIssues + $registerIssues;
                                        @endphp
                                        {{ $totalAlerts > 0 ? $totalAlerts : '' }}
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" style="min-width: 280px;">
                                    <!-- Header -->
                                    <li class="dropdown-header d-flex align-items-center justify-content-between">
                                        <span>Management Panel</span>
                                        <span class="badge {{ $totalAlerts > 0 ? 'bg-danger' : 'bg-success' }}">{{ $totalAlerts }} Alerts</span>
                                    </li>
                                    <li><div class="dropdown-divider"></div></li>
                                    
                                    <!-- Quick Stats -->
                                    <li class="dropdown-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><i class="bx bx-server me-2"></i>Servers</span>
                                            <div>
                                                <span class="badge bg-primary">{{ \App\Models\Server::count() }}</span>
                                                @if($serverIssues > 0)
                                                    <span class="badge bg-danger ms-1">{{ $serverIssues }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                    <li class="dropdown-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><i class="bx bx-globe me-2"></i>Registers</span>
                                            <div>
                                                <span class="badge bg-info">{{ \App\Models\DomainRegister::count() }}</span>
                                                @if($registerIssues > 0)
                                                    <span class="badge bg-danger ms-1">{{ $registerIssues }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                    <li class="dropdown-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><i class="bx bx-user-voice me-2"></i>Clients</span>
                                            <div>
                                                <span class="badge bg-success">{{ \App\Models\ClientData::count() }}</span>
                                                @if($expiringCount > 0)
                                                    <span class="badge bg-warning ms-1">{{ $expiringCount }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                    <li><div class="dropdown-divider"></div></li>
                                    
                                    <!-- Quick Actions -->
                                    <li><h6 class="dropdown-header">Quick Actions</h6></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.servers.index') }}">
                                            <i class="bx bx-server me-2"></i>
                                            <span>Server Management</span>
                                            <i class="bx bx-right-arrow-alt ms-auto"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.domain-registers.index') }}">
                                            <i class="bx bx-globe me-2"></i>
                                            <span>Domain Register</span>
                                            <i class="bx bx-right-arrow-alt ms-auto"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.client-data.index') }}">
                                            <i class="bx bx-user-voice me-2"></i>
                                            <span>Client Data</span>
                                            <i class="bx bx-right-arrow-alt ms-auto"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.client-data.service-status') }}">
                                            <i class="bx bx-bar-chart me-2"></i>
                                            <span>Service Status Overview</span>
                                            <i class="bx bx-right-arrow-alt ms-auto"></i>
                                        </a>
                                    </li>
                                    <li><div class="dropdown-divider"></div></li>
                                    
                                    <!-- Alerts Summary -->
                                    @if($totalAlerts > 0)
                                    <li><h6 class="dropdown-header text-danger">Alerts Summary</h6></li>
                                    @if($expiringCount > 0)
                                    <li class="dropdown-item text-warning">
                                        <i class="bx bx-time-five me-2"></i>
                                        <span>{{ $expiringCount }} services expiring soon</span>
                                    </li>
                                    @endif
                                    @if($serverIssues > 0)
                                    <li class="dropdown-item text-danger">
                                        <i class="bx bx-error me-2"></i>
                                        <span>{{ $serverIssues }} server issues</span>
                                    </li>
                                    @endif
                                    @if($registerIssues > 0)
                                    <li class="dropdown-item text-danger">
                                        <i class="bx bx-error me-2"></i>
                                        <span>{{ $registerIssues }} register issues</span>
                                    </li>
                                    @endif
                                    @endif
                                </ul>
                            </li>
                            @endif
                            @endif
                            
                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset('vendor/sneat/assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="{{ asset('vendor/sneat/assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block">{{ auth()->user()->name }}</span>
                                                    <small class="text-muted">{{ ucfirst(auth()->user()->role ?? 'Client') }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">My Profile</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bx bx-power-off me-2"></i>
                                                <span class="align-middle">Log Out</span>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @yield('content')
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                {{ config('company.copyright') }}
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="{{ asset('vendor/sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('vendor/sneat/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('vendor/sneat/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('vendor/sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('vendor/sneat/assets/vendor/js/menu.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('vendor/sneat/assets/js/main.js') }}"></script>

    @stack('scripts')

    <!-- Custom Scripts -->
    <script>
        // Coming Soon function
        function comingSoon() {
            alert('This feature is coming soon!');
        }

        // Contact Support function
        function contactSupport() {
            const message = encodeURIComponent('{{ config('company.support_messages.default') }}');
            window.open(`{{ config('company.whatsapp_url') }}?text=${message}`, '_blank');
        }

        // Toggle sidebar function
        function toggleSidebar() {
            const layoutMenu = document.getElementById('layout-menu');
            const layoutContainer = document.querySelector('.layout-container');
            
            if (layoutMenu && layoutContainer) {
                layoutMenu.classList.toggle('menu-collapsed');
                layoutContainer.classList.toggle('layout-menu-collapsed');
                
                // Save state to localStorage
                const isCollapsed = layoutMenu.classList.contains('menu-collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            }
        }

        // Initialize sidebar state from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            const layoutMenu = document.getElementById('layout-menu');
            const layoutContainer = document.querySelector('.layout-container');
            
            if (isCollapsed && layoutMenu && layoutContainer) {
                layoutMenu.classList.add('menu-collapsed');
                layoutContainer.classList.add('layout-menu-collapsed');
            }
            
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>

</html>
