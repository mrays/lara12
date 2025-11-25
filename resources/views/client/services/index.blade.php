@extends('layouts.sneat-dashboard')

@section('title', 'My Services')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">My Services</h5>
                        <small class="text-muted">Manage all your active services</small>
                    </div>
                    <div>
                        <button class="btn btn-primary" onclick="requestNewService()">
                            <i class="bx bx-plus me-1"></i>Request New Service
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services List -->
    <div class="row mt-4">
        @forelse($services as $service)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ $service->name }}</h6>
                        @switch($service->status)
                            @case('Active')
                                <span class="badge bg-success">ACTIVE</span>
                                @break
                            @case('Suspended')
                                <span class="badge bg-warning">SUSPENDED</span>
                                @break
                            @case('Terminated')
                                <span class="badge bg-danger">TERMINATED</span>
                                @break
                            @default
                                <span class="badge bg-secondary">{{ strtoupper($service->status) }}</span>
                        @endswitch
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Product:</small>
                            <div>{{ $service->product }}</div>
                        </div>
                        
                        @if($service->domain)
                            <div class="mb-3">
                                <small class="text-muted">Domain:</small>
                                <div>{{ $service->domain }}</div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <small class="text-muted">Billing Cycle:</small>
                            <div>{{ $service->translated_billing_cycle }} - Rp {{ number_format($service->price, 0, ',', '.') }}</div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Next Due:</small>
                            <div>
                                @if($service->due_date)
                                    {{ $service->due_date->format('M d, Y') }}
                                    @if($service->due_date->isPast())
                                        <span class="badge bg-danger ms-1">Overdue</span>
                                    @elseif($service->due_date->diffInDays() <= 7)
                                        <span class="badge bg-warning ms-1">Due Soon</span>
                                    @endif
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex gap-2">
                            <a href="{{ route('client.services.manage', $service) }}" class="btn btn-primary btn-sm flex-fill">
                                <i class="bx bx-cog me-1"></i>Manage
                            </a>
                            @if($service->status === 'Active')
                                <button class="btn btn-success btn-sm flex-fill" onclick="loginDashboard('{{ $service->domain }}')">
                                    <i class="bx bx-log-in me-1"></i>Login
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-package" style="font-size: 4rem; color: #ddd;"></i>
                        <h5 class="mt-3">No Services Found</h5>
                        <p class="text-muted">You don't have any services yet. Contact us to get started!</p>
                        <button class="btn btn-primary" onclick="requestNewService()">
                            <i class="bx bx-plus me-1"></i>Request New Service
                        </button>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<script>
function loginDashboard(domain) {
    if (domain) {
        window.open('https://' + domain + '/admin', '_blank');
    } else {
        alert('No dashboard URL available for this service');
    }
}

function requestNewService() {
    // WhatsApp link for new service request
    const message = encodeURIComponent('{{ config('company.support_messages.general_inquiry') }}');
    window.open(`{{ config('company.whatsapp_url') }}?text=${message}`, '_blank');
}
</script>
@endsection
