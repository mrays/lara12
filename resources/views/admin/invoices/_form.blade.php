@csrf
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Client <span class="text-danger">*</span></label>
        <select name="client_id" class="form-select" required>
            <option value="">-- Choose Client --</option>
            @foreach($clients as $cl)
                <option value="{{ $cl->id }}" {{ (old('client_id', $invoice->client_id ?? '') == $cl->id) ? 'selected':'' }}>
                    {{ $cl->name }} ({{ $cl->email }})
                </option>
            @endforeach
        </select>
        @error('client_id')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Invoice No <span class="text-danger">*</span></label>
        <input type="text" name="invoice_no" class="form-control" 
               value="{{ old('invoice_no', $invoice->invoice_no ?? 'INV-' . date('Ymd') . '-' . rand(1000,9999)) }}" 
               placeholder="INV-20241124-1234" required>
        @error('invoice_no')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Due Date <span class="text-danger">*</span></label>
        <input type="date" name="due_date" class="form-control" 
               value="{{ old('due_date', isset($invoice->due_date) ? $invoice->due_date->format('Y-m-d') : date('Y-m-d', strtotime('+30 days'))) }}" 
               required>
        @error('due_date')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Amount <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="number" name="amount" class="form-control" 
                   value="{{ old('amount', $invoice->amount ?? '') }}" 
                   placeholder="0" step="0.01" min="0" required>
        </div>
        @error('amount')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="Unpaid" {{ (old('status', $invoice->status ?? 'Unpaid')=='Unpaid') ? 'selected':'' }}>Unpaid</option>
            <option value="Paid" {{ (old('status', $invoice->status ?? '')=='Paid') ? 'selected':'' }}>Paid</option>
            <option value="Overdue" {{ (old('status', $invoice->status ?? '')=='Overdue') ? 'selected':'' }}>Overdue</option>
            <option value="Cancelled" {{ (old('status', $invoice->status ?? '')=='Cancelled') ? 'selected':'' }}>Cancelled</option>
            <option value="Sedang Dicek" {{ (old('status', $invoice->status ?? '')=='Sedang Dicek') ? 'selected':'' }}>Sedang Dicek</option>
            <option value="Lunas" {{ (old('status', $invoice->status ?? '')=='Lunas') ? 'selected':'' }}>Lunas</option>
            <option value="Belum Lunas" {{ (old('status', $invoice->status ?? '')=='Belum Lunas') ? 'selected':'' }}>Belum Lunas</option>
        </select>
        @error('status')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3" 
                  placeholder="Invoice description or notes">{{ old('description', $invoice->description ?? '') }}</textarea>
        @error('description')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
</div>
