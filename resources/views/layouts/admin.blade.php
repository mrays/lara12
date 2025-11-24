<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel')</title>

    <!-- Sneat CSS -->
    <link rel="stylesheet" href="/vendor/sneat/assets/vendor/css/core.css" />
    <link rel="stylesheet" href="/vendor/sneat/assets/vendor/css/theme-default.css" />
    <link rel="stylesheet" href="/vendor/sneat/assets/css/demo.css" />

    <link rel="stylesheet" href="/vendor/sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="/vendor/sneat/assets/vendor/libs/apex-charts/apex-charts.css" />
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            {{-- Sidebar --}}
            @include('admin.partials.sidebar')

            <div class="layout-page">
                {{-- Navbar --}}
                @include('admin.partials.navbar')

                <div class="content-wrapper">
                    @yield('content')
                </div>
            </div>

        </div>
    </div>

    <!-- Sneat JS -->
    <script src="/vendor/sneat/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="/vendor/sneat/assets/vendor/libs/popper/popper.js"></script>
    <script src="/vendor/sneat/assets/vendor/js/bootstrap.js"></script>
    <script src="/vendor/sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="/vendor/sneat/assets/vendor/js/menu.js"></script>

    <script src="/vendor/sneat/assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="/vendor/sneat/assets/js/main.js"></script>

    @yield('scripts')
</body>
</html>
