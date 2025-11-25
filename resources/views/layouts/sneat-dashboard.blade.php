<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('vendor/sneat/assets/') }}" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('vendor/sneat/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/sneat/assets/vendor/fonts/boxicons.css') }}" />

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
            
            .menu-icon {
                font-size: 1.1rem !important;
                margin-right: 0.75rem !important;
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
        
        /* Sidebar Toggle Fix */
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
            @include('client.partials.sidebar')
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
