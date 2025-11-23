@extends('layouts.admin')

@section('title', 'Services')

@section('content')
<div class="container-xxl">
  <div class="card"><div class="card-header"><h5>Edit Service</h5></div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.services.update',$service) }}">
        @method('PUT')
        @include('admin.services._form')
        <div class="text-end"><a class="btn btn-secondary" href="{{ route('admin.services.index') }}">Cancel</a> <button class="btn btn-primary">Save</button></div>
      </form>
    </div>
  </div>
</div>
@endsection
