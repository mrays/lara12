@extends('layouts.admin')

@section('title', 'Services')

@section('content')
<div class="container-xxl">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h5>Services</h5>
      <a href="{{ route('admin.services.create') }}" class="btn btn-primary">New Service</a>
    </div>
    <div class="card-body">
      <table class="table">
        <thead><tr><th>#</th><th>Product</th><th>Domain</th><th>Client</th><th>Due Date</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
          @foreach($services as $s)
          <tr>
            <td>{{ $s->id }}</td>
            <td>{{ $s->product }}</td>
            <td>{{ $s->domain ?? '-' }}</td>
            <td>{{ $s->client->name ?? '-' }}</td>
            <td>{{ optional($s->due_date)->format('Y-m-d') }}</td>
            <td><span class="badge {{ $s->status=='Active'?'bg-success':'bg-secondary' }}">{{ $s->status }}</span></td>
            <td>
              <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.services.edit',$s) }}">Edit</a>
              <form action="{{ route('admin.services.destroy',$s) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="mt-3">{{ $services->links() }}</div>
    </div>
  </div>
</div>
@endsection