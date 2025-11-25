@extends('layouts.sneat-dashboard')

@section('title', 'Order Berhasil')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Message -->
            <div class="card mb-4">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <div class="avatar avatar-xl mx-auto">
                            <span class="avatar-initial rounded-circle bg-success">
                                <i class="bx bx-check fs-1"></i>
                            </span>
                        </div>
                    </div>
                    <h4 class="mb-2">Order Berhasil Dibuat!</h4>
                    <p class="text-muted mb-4">
                        Terima kasih atas order Anda. Silakan lakukan pembayaran untuk mengaktifkan layanan.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('client.invoices.show', $invoice->id) }}" class="btn btn-primary">
                            <i class="bx bx-receipt me-1"></i>Lihat Invoice
                        </a>
                        <a href="{{ route('client.invoices.pdf', $invoice->id) }}" class="btn btn-outline-primary" target="_blank">
                            <i class="bx bx-printer me-1"></i>Cetak Invoice
                        </a>
                    </div>
                </div>
            </div>

            <!-- Invoice Preview -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bx bx-file me-2"></i>Detail Invoice
                    </h6>
                    <span class="badge bg-warning">{{ $invoice->status }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Invoice Number</h6>
                            <p class="fw-semibold">{{ $invoice->invoice_number }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="text-muted mb-2">Tanggal</h6>
                            <p class="fw-semibold">{{ $invoice->created_at->format('d M Y') }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Ditagihkan Kepada</h6>
                            <p class="mb-1 fw-semibold">{{ auth()->user()->name }}</p>
                            <p class="mb-1 text-muted">{{ auth()->user()->email }}</p>
                            @if(auth()->user()->whatsapp)
                                <p class="mb-0 text-muted">{{ auth()->user()->whatsapp }}</p>
                            @endif
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="text-muted mb-2">Jatuh Tempo</h6>
                            <p class="fw-semibold text-danger">{{ $invoice->due_date->format('d M Y') }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- Items -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>Deskripsi</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Package Item -->
                                <tr>
                                    <td>
                                        <strong>{{ $invoice->service->product ?? 'Service' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $invoice->service->package->name ?? 'Package' }} ({{ $invoice->service->billing_cycle ?? 'annually' }})</small>
                                    </td>
                                    <td class="text-end">Rp {{ number_format($invoice->service->package->base_price ?? $invoice->total_amount, 0, ',', '.') }}</td>
                                </tr>
                                
                                <!-- Domain Item -->
                                @if($invoice->service->domain)
                                <tr>
                                    <td>
                                        <strong>Domain</strong>
                                        <br>
                                        <small class="text-muted">{{ $invoice->service->domain }}</small>
                                        @php
                                            $isDomainFree = false;
                                            // Check new multiple free domains system
                                            if($invoice->service->package && $invoice->service->package->freeDomains) {
                                                $domainExtension = $invoice->service->package->freeDomains
                                                    ->where('domain_extension_id', $invoice->service->domain_extension_id)
                                                    ->first();
                                                $isDomainFree = $domainExtension && $domainExtension->is_free;
                                            }
                                            // Fallback to old single domain system
                                            elseif($invoice->service->package && $invoice->service->package->domain_extension_id == $invoice->service->domain_extension_id) {
                                                $isDomainFree = $invoice->service->package->is_domain_free;
                                            }
                                        @endphp
                                        @if($isDomainFree)
                                            <br><small class="text-success"><i class="bx bx-gift me-1"></i>Domain Gratis</small>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($isDomainFree)
                                            <span class="text-success fw-bold">GRATIS</span>
                                        @else
                                            Rp {{ number_format($invoice->total_amount - ($invoice->service->package->base_price ?? 0), 0, ',', '.') }}
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th class="text-end text-primary fs-5">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <hr>

                    <!-- Payment Actions -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>
                                Layanan akan aktif setelah pembayaran dikonfirmasi.
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('client.invoices.pay', $invoice->id) }}" class="btn btn-success">
                                <i class="bx bx-credit-card me-1"></i>Bayar Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-list-check me-2"></i>Langkah Selanjutnya
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <div class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded-circle bg-label-primary">1</span>
                        </div>
                        <div>
                            <h6 class="mb-1">Lakukan Pembayaran</h6>
                            <p class="text-muted mb-0 small">Bayar invoice sebelum tanggal jatuh tempo untuk menghindari pembatalan order.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-3">
                        <div class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded-circle bg-label-primary">2</span>
                        </div>
                        <div>
                            <h6 class="mb-1">Konfirmasi Pembayaran</h6>
                            <p class="text-muted mb-0 small">Setelah pembayaran, tim kami akan memverifikasi dan mengaktifkan layanan Anda.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start">
                        <div class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded-circle bg-label-success">3</span>
                        </div>
                        <div>
                            <h6 class="mb-1">Layanan Aktif</h6>
                            <p class="text-muted mb-0 small">Anda akan menerima notifikasi email ketika layanan sudah aktif dan siap digunakan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('client.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
