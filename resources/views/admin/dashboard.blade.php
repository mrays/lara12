@extends('layouts.app')

@section('content')

{{-- Sneat CSS --}}
<link rel="stylesheet" href="{{ asset('vendor/sneat/assets/vendor/css/core.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/sneat/assets/vendor/css/theme-default.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/sneat/assets/css/demo.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/sneat/assets/vendor/fonts/boxicons.css') }}">
<div class="d-flex justify-content-end mb-3">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn btn-danger">
            <i class="bx bx-power-off"></i> Logout
        </button>
    </form>
</div>
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- TOP CARDS --}}
    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card" style="background:#e9f3ff;">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <h3 class="fw-bold">{{ $stats['active_services'] ?? 0 }}</h3>
                        <span>Active Service</span>
                    </div>
                    <i class="bx bx-package fs-1"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card" style="background:#fff3db;">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <h3 class="fw-bold">{{ $stats['invoice_unpaid'] ?? 0 }}</h3>
                        <span>Invoice Unpaid</span>
                    </div>
                    <i class="bx bx-receipt fs-1"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card" style="background:#fbe8e8;">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <h3 class="fw-bold">{{ $stats['tickets_open'] ?? 0 }}</h3>
                        <span>Ticket Open</span>
                    </div>
                    <i class="bx bx-headphone fs-1"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- INVOICES & GUIDES --}}
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Invoices</h5>
                </div>
                <div class="card-body">

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Due Date</th>
                                <th>No Invoice</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($invoices as $inv)
                                <tr>
                                    <td>{{ $inv->due_date }}</td>
                                    <td>{{ $inv->number }}</td>
                                    <td>{{ $inv->amount }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <a class="btn btn-primary" href="#">View All Invoices</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        {{-- Guides --}}
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Guides</h5>
                </div>
                <div class="card-body">
                    <ul>
                        <li><a href="#">PrestaShop: Fungsi, Fitur, Install...</a></li>
                        <li><a href="#">Mico, Asisten AI Pengganti Clippy...</a></li>
                        <li><a href="#">Domain .Photo: Kelebihan...</a></li>
                        <li><a href="#">Atlas ChatGPT: Cara Baru Browsing...</a></li>
                        <li><a href="#">Domain .Toys: Cara Membelinya...</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- SERVICES TABLE --}}
    <div class="card">
        <div class="card-header">
            <h5>List all products & services</h5>
        </div>
        <div class="card-body">

            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Product or Service</th>
                        <th>Price</th>
                        <th>Registration</th>
                        <th>Due Date</th>
                        <th>IP</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($services as $s)
                        <tr>
                            <td>
                                <strong>{{ $s->product }}</strong><br>
                                <small class="text-muted">{{ $s->domain }}</small>
                            </td>
                            <td>{{ $s->price }} <br><small>{{ $s->billing_cycle }}</small></td>
                            <td>{{ $s->registration }}</td>
                            <td>{{ $s->due_date }}</td>
                            <td>{{ $s->ip }}</td>
                            <td>
                                <span class="badge bg-success">{{ $s->status }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>

{{-- JS --}}
<script src="{{ asset('vendor/sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('vendor/sneat/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('vendor/sneat/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('vendor/sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('vendor/sneat/assets/vendor/js/menu.js') }}"></script>
<script src="{{ asset('vendor/sneat/assets/js/main.js') }}"></script>

@endsection
