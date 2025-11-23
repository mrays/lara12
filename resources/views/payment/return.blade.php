@extends('layouts.admin')
@section('content')
<div class="container">
  <h3>Payment result</h3>
  <pre>{{ print_r($payload, true) }}</pre>
  <a href="{{ route('admin.invoices.index') }}" class="btn btn-primary">Back to invoices</a>
</div>
@endsection
