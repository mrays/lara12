@extends('layouts.admin')

@section('title', 'Services')

@section('content')
<div class="container-xxl">
  <div class="card">
    <div class="card-header"><h5>Service #{{ $service->id }}</h5></div>
    <div class="card-body">
      <p><strong>Product:</strong> {{ $service->product }}</p>
      <p><strong>Domain:</strong> {{ $service->domain }}</p>
      <p><strong>Client:</strong> {{ $service->client->name ?? '-' }}</p>
      <p><strong>Due Date:</strong> {{ optional($service->due_date)->format('Y-m-d') }}</p>
      <a class="btn btn-secondary" href="{{ route('admin.services.index') }}">Back</a>
    </div>
  </div>
</div>
@endsection
