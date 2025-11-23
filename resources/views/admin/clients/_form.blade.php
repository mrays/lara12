@csrf
<div class="mb-3">
  <label class="form-label">Name</label>
  <input name="name" class="form-control" value="{{ old('name', $client->name ?? '') }}" required>
</div>
<div class="mb-3">
  <label class="form-label">Email</label>
  <input name="email" class="form-control" value="{{ old('email', $client->email ?? '') }}" required>
</div>
<div class="mb-3">
  <label class="form-label">Phone</label>
  <input name="phone" class="form-control" value="{{ old('phone', $client->phone ?? '') }}">
</div>
<div class="mb-3">
  <label class="form-label">Status</label>
  <select name="status" class="form-select">
    <option value="Active" {{ (old('status', $client->status ?? '')=='Active') ? 'selected':'' }}>Active</option>
    <option value="Suspended" {{ (old('status', $client->status ?? '')=='Suspended') ? 'selected':'' }}>Suspended</option>
    <option value="Cancelled" {{ (old('status', $client->status ?? '')=='Cancelled') ? 'selected':'' }}>Cancelled</option>
  </select>
</div>
