@extends('layouts.admin')

@section('title', 'Services')

@section('content')
<div class="container-xxl">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h5>Clients</h5>
      <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">New Client</a>
    </div>
    <div class="card-body">
      <table class="table">
        <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
          @foreach($clients as $c)
          <tr>
            <td>{{ $c->id }}</td>
            <td><a href="{{ route('admin.clients.show',$c) }}">{{ $c->name }}</a></td>
            <td>{{ $c->email }}</td>
            <td>{{ $c->phone ?? '-' }}</td>
            <td><span class="badge {{ $c->status=='Active'?'bg-success':'bg-secondary' }}">{{ $c->status }}</span></td>
            <td>
              <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.clients.edit',$c) }}">Edit</a>
              <form action="{{ route('admin.clients.destroy',$c) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="mt-3">{{ $clients->links() }}</div>
    </div>
  </div>
</div>
@endsection
