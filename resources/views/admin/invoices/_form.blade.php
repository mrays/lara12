@csrf
<div class="mb-3">
  <label class="form-label">Client</label>
  <select name="client_id" class="form-select" required>
    <option value="">-- choose client --</option>
    @foreach($clients as $cl)
      <option value="{{ $cl->id }}" {{ (old('client_id', $invoice->client_id ?? '') == $cl->id) ? 'selected':'' }}>{{ $cl->name }} ({{ $cl->email }})</option>
    @endforeach
  </select>
</div>
<div class="mb-3">
  <label class="form-label">Invoice No</label>
  <input name="invoice_no" class="form-control" value="{{ old('invoice_no', $invoice->invoice_no ?? '') }}" required>
</div>
<div class="mb-3">
  <label class="form-label">Due Date</label>
  <input type="date" name="due_date" class="form-control" value="{{ old('due_date', isset($invoice->due_date)?$invoice->due_date->format('Y-m-d'): '') }}">
</div>
<div class="mb-3">
  <label class="form-label">Amount</label>
  <input name="amount" class="form-control" value="{{ old('amount', $invoice->amount ?? '') }}" required>
</div>
<div class="mb-3">
  <label class="form-label">Status</label>
  <select name="status" class="form-select">
    <option value="Unpaid" {{ (old('status', $invoice->status ?? '')=='Unpaid') ? 'selected':'' }}>Unpaid</option>
    <option value="Paid" {{ (old('status', $invoice->status ?? '')=='Paid') ? 'selected':'' }}>Paid</option>
    <option value="Past Due" {{ (old('status', $invoice->status ?? '')=='Past Due') ? 'selected':'' }}>Past Due</option>
  </select>
</div>
