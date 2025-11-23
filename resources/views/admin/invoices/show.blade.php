@extends('layouts.admin')

@section('title', 'Services')

@section('content')
<div class="container-xxl">
  <div class="card">
    <div class="card-header"><h5>Invoice #{{ $invoice->invoice_no }}</h5></div>
    <div class="card-body">
      <p><strong>Client:</strong> {{ $invoice->client->name ?? '-' }}</p>
      <p><strong>Amount:</strong> {{ number_format($invoice->amount,2) }}</p>
      <a class="btn btn-secondary" href="{{ route('admin.invoices.index') }}">Back</a>
    </div>
  </div>
</div>
@endsection
