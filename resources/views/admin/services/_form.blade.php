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
  <label class="form-label">Product</label>
  <input name="product" class="form-control" value="{{ old('product', $service->product ?? '') }}" required>
</div>
<div class="mb-3">
  <label class="form-label">Domain</label>
  <input name="domain" class="form-control" value="{{ old('domain', $service->domain ?? '') }}">
</div>
<div class="mb-3">
  <label class="form-label">Price</label>
  <input name="price" class="form-control" value="{{ old('price', $service->price ?? '') }}" required>
</div>
<div class="mb-3">
  <label class="form-label">Billing Cycle</label>
  <input name="billing_cycle" class="form-control" value="{{ old('billing_cycle', $service->billing_cycle ?? '') }}">
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
    <option value="Suspended" {{ (old('status', $service->status ?? '')=='Suspended') ? 'selected':'' }}>Suspended</option>
    <option value="Cancelled" {{ (old('status', $service->status ?? '')=='Cancelled') ? 'selected':'' }}>Cancelled</option>
  </select>
</div>
