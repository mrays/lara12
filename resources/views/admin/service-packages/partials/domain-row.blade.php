<div class="domain-row border rounded p-3 mb-3" data-index="{{ $index }}">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0">
            <i class="bx bx-globe me-1"></i>Domain #{{ $index + 1 }}
        </h6>
        @if($index > 0)
            <button type="button" class="btn btn-sm btn-danger" onclick="removeDomainRow({{ $index }})">
                <i class="bx bx-trash me-1"></i>Hapus
            </button>
        @endif
    </div>
    
    <div class="row">
        <div class="col-md-3 mb-3">
            <label class="form-label">
                Domain Extension
            </label>
            <select class="form-select domain-extension-select" 
                    name="free_domains[{{ $index }}][domain_extension_id]"
                    onchange="updateDomainPreview({{ $index }})">
                <option value="">Pilih Domain</option>
                @foreach($groupedDomains as $extension => $domains)
                    <optgroup label=".{{ $extension }}">
                        @foreach($domains as $domain)
                            <option value="{{ $domain->id }}" 
                                    {{ $freeDomain && $freeDomain->domain_extension_id == $domain->id ? 'selected' : '' }}
                                    data-price="{{ $domain->price }}"
                                    data-duration="{{ $domain->duration_years }}"
                                    data-extension="{{ $domain->extension }}">
                                .{{ $domain->extension }} ({{ $domain->duration_years }} tahun) - {{ $domain->formatted_price }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @error("free_domains.{$index}.domain_extension_id")
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-2 mb-3">
            <label class="form-label">
                Durasi (Tahun)
            </label>
            <select class="form-select domain-duration-select" 
                    name="free_domains[{{ $index }}][duration_years]"
                    onchange="updateDomainPreview({{ $index }})">
                <option value="">Pilih</option>
                @for($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}" {{ $freeDomain && $freeDomain->duration_years == $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
            @error("free_domains.{$index}.duration_years")
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-2 mb-3">
            <label class="form-label">
                Diskon (%)
            </label>
            <input type="number" 
                   class="form-control domain-discount-input" 
                   name="free_domains[{{ $index }}][discount_percent]"
                   value="{{ $freeDomain ? $freeDomain->discount_percent : '0' }}"
                   min="0" 
                   max="100" 
                   step="0.01"
                   oninput="updateDomainPreview({{ $index }})">
            @error("free_domains.{$index}.discount_percent")
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-2 mb-3">
            <label class="form-label">
                Harga Promo
            </label>
            <div class="form-control-plaintext">
                <span class="domain-price-display" id="domainPriceDisplay{{ $index }}">
                    {{ $freeDomain ? $freeDomain->formatted_price : '-' }}
                </span>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <label class="form-label">
                Status
            </label>
            <div class="form-check form-switch mt-2">
                <input class="form-check-input domain-free-checkbox" 
                       type="checkbox" 
                       name="free_domains[{{ $index }}][is_free]" 
                       value="1" 
                       {{ $freeDomain && $freeDomain->is_free ? 'checked' : '' }}
                       onchange="toggleFreeDomain({{ $index }})">
                <label class="form-check-label">
                    <i class="bx bx-gift me-1"></i>Domain Gratis
                </label>
            </div>
        </div>
    </div>

    <!-- Preview for this domain -->
    <div class="domain-preview" id="domainPreview{{ $index }}" style="display: none;">
        <div class="alert alert-success alert-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>Domain:</strong> <span class="preview-domain">-</span><br>
                    <strong>Durasi:</strong> <span class="preview-duration">-</span><br>
                    <strong>Harga Normal:</strong> <span class="preview-normal-price">-</span><br>
                    <strong>Diskon:</strong> <span class="preview-discount">-</span><br>
                    <strong>Harga Promo:</strong> <span class="preview-promo-price">-</span>
                </div>
                <div class="text-end">
                    <span class="badge bg-success preview-status">-</span>
                </div>
            </div>
        </div>
    </div>
</div>
