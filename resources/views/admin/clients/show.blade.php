@extends('layouts.admin')

@section('title', 'Services')

@section('content')
<div class="container-xxl">
  <div class="card"><div class="card-header"><h5>Client: {{ $client->name }}</h5></div>
    <div class="card-body">
      <p><strong>Email:</strong> {{ $client->email }}</p>
      <p><strong>Phone:</strong> {{ $client->phone }}</p>
      <p><strong>Status:</strong> {{ $client->status }}</p>
      <a class="btn btn-secondary" href="{{ route('admin.clients.index') }}">Back</a>
    </div>
  </div>
</div>
@endsection
