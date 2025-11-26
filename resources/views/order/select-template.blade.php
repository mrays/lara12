@extends('layouts.guest')

@section('title', 'Select Template - Order')

@section('content')
<div class="container-xxl py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Progress Steps -->
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex flex-column align-items-center">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bx bx-check"></i>
                        </div>
                        <small class="mt-2 text-success">Domain</small>
                    </div>
                    <div class="flex-grow-1 mx-2">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-success" style="width: 50%"></div>
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bx bx-palette"></i>
                        </div>
                        <small class="mt-2 text-primary">Template</small>
                    </div>
                    <div class="flex-grow-1 mx-2">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-primary" style="width: 25%"></div>
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-center">
                        <div class="bg-light text-muted rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bx bx-user"></i>
                        </div>
                        <small class="mt-2 text-muted">Data Diri</small>
                    </div>
                    <div class="flex-grow-1 mx-2">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-light" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-center">
                        <div class="bg-light text-muted rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bx bx-package"></i>
                        </div>
                        <small class="mt-2 text-muted">Paket</small>
                    </div>
                </div>
            </div>

            <!-- Selected Domain Info -->
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="alert-heading mb-1">
                            <i class="bx bx-globe me-2"></i>Selected Domain
                        </h6>
                        <span class="fs-5 fw-bold">{{ session('order.full_domain') }}</span>
                    </div>
                    <div class="text-end">
                        <small class="d-block text-muted">Domain Registration</small>
                        <span class="fs-5 fw-bold text-success">{{ $domainExtension->formatted_price }}</span>
                    </div>
                </div>
            </div>

            <!-- Template Selection -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bx bx-palette me-2"></i>Choose Your Website Template
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('order.post-select-template') }}" method="POST">
                        @csrf
                        
                        <!-- Template Categories -->
                        <div class="mb-4">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-outline-primary btn-sm filter-btn active" data-category="all">
                                    All Templates
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm filter-btn" data-category="Business">
                                    Business
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm filter-btn" data-category="Portfolio">
                                    Portfolio
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm filter-btn" data-category="E-commerce">
                                    E-commerce
                                </button>
                            </div>
                        </div>

                        <!-- Templates Grid -->
                        <div class="row g-4">
                            @foreach($templates as $template)
                                <div class="col-lg-4 col-md-6 template-item" data-category="{{ $template['category'] }}">
                                    <div class="card h-100 template-card {{ old('template_id') == $template['id'] ? 'border-primary' : '' }}" 
                                         style="cursor: pointer;"
                                         onclick="selectTemplate({{ $template['id'] }})">
                                        
                                        <!-- Template Preview -->
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <i class="bx bx-image text-muted" style="font-size: 60px;"></i>
                                            <!-- You can replace this with actual template preview images -->
                                        </div>
                                        
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0">{{ $template['name'] }}</h6>
                                                @if($template['is_free'])
                                                    <span class="badge bg-success">FREE</span>
                                                @else
                                                    <span class="badge bg-warning">Premium</span>
                                                @endif
                                            </div>
                                            
                                            <p class="card-text text-muted small mb-3">{{ $template['description'] }}</p>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-light text-dark">{{ $template['category'] }}</span>
                                                @if(!$template['is_free'])
                                                    <span class="fw-bold text-primary">
                                                        +Rp {{ number_format($template['price'], 0, ',', '.') }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <div class="d-grid mt-3">
                                                <button type="button" 
                                                        class="btn {{ old('template_id') == $template['id'] ? 'btn-primary' : 'btn-outline-primary' }} btn-sm"
                                                        onclick="selectTemplate({{ $template['id'] }})">
                                                    {{ old('template_id') == $template['id'] ? 'Selected' : 'Select Template' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Skip Template Option -->
                        <div class="text-center mt-4 pt-4 border-top">
                            <p class="text-muted mb-3">
                                <i class="bx bx-info-circle me-1"></i>
                                Don't see a template you like? You can skip this step and choose later.
                            </p>
                            <button type="button" class="btn btn-outline-secondary" onclick="selectTemplate(0)">
                                <i class="bx bx-skip-next me-2"></i>Skip Template Selection
                            </button>
                        </div>

                        <!-- Hidden input for selected template -->
                        <input type="hidden" name="template_id" id="selectedTemplateId" value="{{ old('template_id', 1) }}" required>

                        <!-- Continue Button -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success btn-lg" id="continueBtn">
                                <i class="bx bx-right-arrow-alt me-2"></i>Continue to Account Registration
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Template Features -->
            <div class="row mt-5">
                <div class="col-md-4 text-center mb-4">
                    <div class="feature-icon bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bx bx-mobile fs-3 text-primary"></i>
                    </div>
                    <h6>Mobile Responsive</h6>
                    <p class="text-muted small">All templates are fully responsive and mobile-friendly.</p>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="feature-icon bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bx bx-edit fs-3 text-success"></i>
                    </div>
                    <h6>Easy Customization</h6>
                    <p class="text-muted small">Customize colors, fonts, and content with our easy editor.</p>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="feature-icon bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bx bx-rocket fs-3 text-info"></i>
                    </div>
                    <h6>Fast Loading</h6>
                    <p class="text-muted small">Optimized templates for fast loading and better SEO.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function selectTemplate(templateId) {
    // Update hidden input
    document.getElementById('selectedTemplateId').value = templateId;
    
    // Update card styles
    document.querySelectorAll('.template-card').forEach(card => {
        card.classList.remove('border-primary');
        const btn = card.querySelector('button');
        if (btn) {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-primary');
            btn.textContent = 'Select Template';
        }
    });
    
    if (templateId > 0) {
        const selectedCard = document.querySelector(`[onclick="selectTemplate(${templateId})"]`);
        if (selectedCard) {
            selectedCard.classList.add('border-primary');
            const selectedBtn = selectedCard.querySelector('button');
            if (selectedBtn) {
                selectedBtn.classList.remove('btn-outline-primary');
                selectedBtn.classList.add('btn-primary');
                selectedBtn.textContent = 'Selected';
            }
        }
    }
}

// Category filtering
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const category = this.dataset.category;
        
        // Update active button
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // Filter templates
        document.querySelectorAll('.template-item').forEach(item => {
            if (category === 'all' || item.dataset.category === category) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

// Initialize if template is pre-selected
@if(old('template_id'))
    selectTemplate({{ old('template_id') }});
@else
    selectTemplate(1); // Select first template by default
@endif
</script>
@endsection
