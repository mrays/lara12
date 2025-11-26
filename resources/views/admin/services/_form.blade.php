@csrf
<div class="mb-3">
  <label class="form-label">Client</label>
  <select name="client_id" class="form-select" required>
    <option value="">-- choose client --</option>
    @foreach($clients as $cl)
      <option value="{{ $cl->id }}" {{ (old('client_id', $service->client_id ?? '') == $cl->id) ? 'selected':'' }}>{{ $cl->name }} ({{ $cl->email }})</option>
    @endforeach
  </select>
</div>

<div class="mb-3">
  <label class="form-label">Service Package</label>
  <select name="package_id" class="form-select" id="package_select" onchange="updatePackageDetails()">
    <option value="">-- choose service package --</option>
    @if(isset($packages))
      @php
        $standardPackages = $packages->where('is_custom', false);
        $customPackages = $packages->where('is_custom', true);
      @endphp
      
      @if($standardPackages->count() > 0)
        <optgroup label="📦 Standard Packages">
          @foreach($standardPackages as $pkg)
            <option value="{{ $pkg->id }}" 
                    data-name="{{ $pkg->name }}" 
                    data-description="{{ $pkg->description }}" 
                    data-price="{{ $pkg->base_price }}"
                    data-custom="0"
                    {{ (old('package_id', $service->package_id ?? '') == $pkg->id) ? 'selected':'' }}>
              {{ $pkg->name }} - Rp {{ number_format($pkg->base_price, 0, ',', '.') }}
            </option>
          @endforeach
        </optgroup>
      @endif
      
      @if($customPackages->count() > 0)
        <optgroup label="⭐ Custom Packages (Special Pricing)">
          @foreach($customPackages as $pkg)
            <option value="{{ $pkg->id }}" 
                    data-name="{{ $pkg->name }}" 
                    data-description="{{ $pkg->description }}" 
                    data-price="{{ $pkg->base_price }}"
                    data-custom="1"
                    {{ (old('package_id', $service->package_id ?? '') == $pkg->id) ? 'selected':'' }}>
              ⭐ {{ $pkg->name }} - Rp {{ number_format($pkg->base_price, 0, ',', '.') }}
            </option>
          @endforeach
        </optgroup>
      @endif
    @endif
  </select>
  <small class="form-text text-muted">Select a service package or leave empty for custom service. Custom packages (⭐) are for special client pricing.</small>
</div>

<div class="mb-3" id="custom_package_alert" style="display: none;">
  <div class="alert alert-warning">
    <i class="bx bx-star me-1"></i>
    <strong>Custom Package Selected!</strong> This is a special package with custom pricing for specific clients.
  </div>
</div>

<div class="mb-3">
  <label class="form-label">Product/Service Name</label>
  <input name="product" id="product_input" class="form-control" value="{{ old('product', $service->product ?? '') }}" required>
  <small class="form-text text-muted">Will be auto-filled when you select a package</small>
</div>
<div class="mb-3">
  <label class="form-label">Domain</label>
  <input name="domain" class="form-control" value="{{ old('domain', $service->domain ?? '') }}">
</div>
<div class="mb-3">
  <label class="form-label">Custom Price (Rp)</label>
  <input name="price" id="price_input" type="number" class="form-control" value="{{ old('price', $service->price ?? '') }}" step="1000" min="0" required>
  <small class="form-text text-muted">Will be auto-filled from package base price, but you can customize it</small>
</div>

<div class="mb-3" id="package_description" style="display: none;">
  <label class="form-label">Package Description</label>
  <div class="alert alert-light" id="description_content"></div>
</div>
<div class="mb-3">
  <label class="form-label">Billing Cycle</label>
  <select name="billing_cycle" class="form-select">
    <option value="">-- choose billing cycle --</option>
    <option value="1M" {{ (old('billing_cycle', $service->billing_cycle ?? '') == '1M') ? 'selected':'' }}>1 Bulan</option>
    <option value="2M" {{ (old('billing_cycle', $service->billing_cycle ?? '') == '2M') ? 'selected':'' }}>2 Bulan</option>
    <option value="3M" {{ (old('billing_cycle', $service->billing_cycle ?? '') == '3M') ? 'selected':'' }}>3 Bulan</option>
    <option value="6M" {{ (old('billing_cycle', $service->billing_cycle ?? '') == '6M') ? 'selected':'' }}>6 Bulan</option>
    <option value="1Y" {{ (old('billing_cycle', $service->billing_cycle ?? '') == '1Y') ? 'selected':'' }}>1 Tahun</option>
    <option value="2Y" {{ (old('billing_cycle', $service->billing_cycle ?? '') == '2Y') ? 'selected':'' }}>2 Tahun</option>
    <option value="3Y" {{ (old('billing_cycle', $service->billing_cycle ?? '') == '3Y') ? 'selected':'' }}>3 Tahun</option>
    <option value="4Y" {{ (old('billing_cycle', $service->billing_cycle ?? '') == '4Y') ? 'selected':'' }}>4 Tahun</option>
  </select>
</div>
<div class="mb-3">
  <label class="form-label">Registration Date</label>
  <input type="date" name="registration_date" class="form-control" value="{{ old('registration_date', isset($service->registration_date)?$service->registration_date->format('Y-m-d'): '') }}">
</div>
<div class="mb-3">
  <label class="form-label">Due Date</label>
  <input type="date" name="due_date" class="form-control" value="{{ old('due_date', isset($service->due_date)?$service->due_date->format('Y-m-d'): '') }}">
</div>
<div class="mb-3">
  <label class="form-label">IP</label>
  <input name="ip" class="form-control" value="{{ old('ip', $service->ip ?? '') }}">
</div>
<div class="mb-3">
  <label class="form-label">Status</label>
  <select name="status" class="form-select">
    <option value="Active" {{ (old('status', $service->status ?? '')=='Active') ? 'selected':'' }}>Active</option>
    <option value="Pending" {{ (old('status', $service->status ?? '')=='Pending') ? 'selected':'' }}>Pending</option>
    <option value="Suspended" {{ (old('status', $service->status ?? '')=='Suspended') ? 'selected':'' }}>Suspended</option>
    <option value="Terminated" {{ (old('status', $service->status ?? '')=='Terminated') ? 'selected':'' }}>Terminated</option>
    <option value="Dibatalkan" {{ (old('status', $service->status ?? '')=='Dibatalkan') ? 'selected':'' }}>Dibatalkan</option>
    <option value="Disuspen" {{ (old('status', $service->status ?? '')=='Disuspen') ? 'selected':'' }}>Disuspen</option>
    <option value="Sedang Dibuat" {{ (old('status', $service->status ?? '')=='Sedang Dibuat') ? 'selected':'' }}>Sedang Dibuat</option>
    <option value="Ditutup" {{ (old('status', $service->status ?? '')=='Ditutup') ? 'selected':'' }}>Ditutup</option>
  </select>
</div>

<script>
function updatePackageDetails() {
    const select = document.getElementById('package_select');
    const selectedOption = select.options[select.selectedIndex];
    const customAlert = document.getElementById('custom_package_alert');
    
    if (selectedOption.value) {
        // Auto-fill product name
        document.getElementById('product_input').value = selectedOption.dataset.name;
        
        // Auto-fill price
        document.getElementById('price_input').value = selectedOption.dataset.price;
        
        // Show package description
        document.getElementById('description_content').innerHTML = selectedOption.dataset.description;
        document.getElementById('package_description').style.display = 'block';
        
        // Show/hide custom package alert
        if (selectedOption.dataset.custom === '1') {
            customAlert.style.display = 'block';
        } else {
            customAlert.style.display = 'none';
        }
    } else {
        // Clear fields if no package selected
        document.getElementById('product_input').value = '';
        document.getElementById('price_input').value = '';
        document.getElementById('package_description').style.display = 'none';
        customAlert.style.display = 'none';
    }
}

// Initialize on page load if package is already selected
document.addEventListener('DOMContentLoaded', function() {
    const packageSelect = document.getElementById('package_select');
    if (packageSelect.value) {
        updatePackageDetails();
    }
});
</script>
