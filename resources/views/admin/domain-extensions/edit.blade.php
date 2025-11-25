@extends('layouts.admin')

@section('title', 'Edit Domain Extension - Admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-edit me-2"></i>Edit Extension Domain: {{ $domainExtension->extension }}
                    </h5>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.domain-extensions.update', $domainExtension) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="extension" class="form-label">
                                        Extension Domain <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">.</span>
                                        <input type="text" 
                                               class="form-control @error('extension') is-invalid @enderror" 
                                               id="extension" 
                                               name="extension" 
                                               value="{{ old('extension', $domainExtension->extension) }}" 
                                               placeholder="com, id, co.id, go.id, ac.id"
                                               required
                                               pattern="[a-z0-9.-]+"
                                               title="Hanya huruf kecil, angka, titik, dan dash (-)">
                                    </div>
                                    @error('extension')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Contoh: com, id, org, net, co.id, go.id, ac.id</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration_years" class="form-label">
                                        Durasi Langganan <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('duration_years') is-invalid @enderror" 
                                            id="duration_years" 
                                            name="duration_years" 
                                            required>
                                        <option value="">Pilih Durasi</option>
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ old('duration_years', $domainExtension->duration_years) == $i ? 'selected' : '' }}>
                                                {{ $i }} Tahun{{ $i > 1 ? '' : '' }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('duration_years')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">
                                        Harga <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" 
                                               class="form-control @error('price') is-invalid @enderror" 
                                               id="price" 
                                               name="price" 
                                               value="{{ old('price', $domainExtension->price) }}" 
                                               placeholder="150000"
                                               required
                                               min="0"
                                               step="0.01">
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Harga dalam Rupiah</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="is_active" class="form-label">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1"
                                               {{ old('is_active', $domainExtension->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Aktif
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Extension domain akan ditampilkan jika aktif</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="3"
                                              placeholder="Deskripsi opsional untuk extension domain">{{ old('description', $domainExtension->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Deskripsi opsional (maks 255 karakter)</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Current Info -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="bx bx-info-circle me-1"></i>Informasi Saat Ini
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Extension:</strong> .{{ $domainExtension->extension }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Durasi:</strong> {{ $domainExtension->duration_years }} Tahun
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Harga:</strong> {{ $domainExtension->formatted_price }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Status:</strong> 
                                            <span class="badge bg-{{ $domainExtension->is_active ? 'success' : 'secondary' }}">
                                                {{ $domainExtension->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($domainExtension->description)
                                        <div class="mt-2">
                                            <strong>Deskripsi:</strong> {{ $domainExtension->description }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.domain-extensions.index') }}" class="btn btn-outline-secondary">
                                        <i class="bx bx-arrow-back me-1"></i> Kembali
                                    </a>
                                    <div>
                                        <button type="reset" class="btn btn-outline-danger me-2">
                                            <i class="bx bx-reset me-1"></i> Reset
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bx-save me-1"></i> Update Extension
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-format price input
    const priceInput = document.getElementById('price');
    priceInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^\d]/g, '');
        if (value) {
            e.target.value = parseInt(value);
        }
    });
    
    // Auto-format extension input
    const extensionInput = document.getElementById('extension');
    extensionInput.addEventListener('input', function(e) {
        // Remove invalid characters, allow letters, numbers, dots, and dashes
        let value = e.target.value.toLowerCase().replace(/[^a-z0-9.-]/g, '');
        // Prevent multiple consecutive dots
        value = value.replace(/\.{2,}/g, '.');
        // Prevent leading or trailing dots
        value = value.replace(/^\.+|\.+$/g, '');
        e.target.value = value;
    });
});
</script>
@endpush
@endsection
