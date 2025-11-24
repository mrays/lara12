<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('vendor/sneat/assets/') }}" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Authentication')</title>

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

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/sneat/assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Custom Auth Styles -->
    <style>
        .authentication-wrapper {
            display: flex;
            flex-basis: 100%;
            min-height: 100vh;
            width: 100%;
        }
        
        .authentication-inner {
            width: 100%;
            max-width: 400px;
            margin: auto;
        }
        
        .card {
            box-shadow: 0 0.25rem 1rem rgba(161, 172, 184, 0.45);
            border: 0;
            border-radius: 0.375rem;
        }
        
        .app-brand {
            margin-bottom: 1.5rem !important;
        }
        
        .app-brand-logo {
            width: 32px;
            height: 32px;
        }
        
        .app-brand-text {
            font-size: 1.25rem;
            letter-spacing: -0.5px;
        }
        
        @media (max-width: 575.98px) {
            .authentication-inner {
                max-width: 100%;
                padding: 0 1rem;
            }
            
            .card {
                margin: 1rem 0;
            }
        }
    </style>

    @stack('styles')

    <!-- Helpers -->
    <script src="{{ asset('vendor/sneat/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('vendor/sneat/assets/js/config.js') }}"></script>
</head>

<body>
    <!-- Content -->
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                @yield('content')
            </div>
        </div>
    </div>
    <!-- / Content -->

    <!-- Core JS -->
    <script src="{{ asset('vendor/sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('vendor/sneat/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('vendor/sneat/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('vendor/sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('vendor/sneat/assets/vendor/js/menu.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('vendor/sneat/assets/js/main.js') }}"></script>

    @stack('scripts')
</body>

</html>
