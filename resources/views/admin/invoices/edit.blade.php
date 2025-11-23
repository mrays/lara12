@extends('layouts.admin')

@section('title', 'Services')

@section('content')
<div class="container-xxl">
  <div class="card"><div class="card-header"><h5>Edit Invoice</h5></div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.invoices.update',$invoice) }}">
        @method('PUT')
        @include('admin.invoices._form')
        <div class="text-end"><a class="btn btn-secondary" href="{{ route('admin.invoices.index') }}">Cancel</a> <button class="btn btn-primary">Save</button></div>
      </form>
    </div>
  </div>
</div>
@endsection
