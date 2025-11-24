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
            <td>{{ $s->client_name ?? '-' }}</td>
            <td>{{ $s->due_date ? date('Y-m-d', strtotime($s->due_date)) : '-' }}</td>
            <td><span class="badge {{ $s->status=='Active'?'bg-success':'bg-secondary' }}">{{ $s->status }}</span></td>
            <td>
              <div class="d-flex gap-1">
                <a class="btn btn-sm btn-outline-info" href="{{ route('admin.services.manage-details', $s->id) }}" title="Manage Client View">
                  <i class="tf-icons bx bx-cog"></i>
                </a>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.services.edit', $s->id) }}" title="Edit Service">
                  <i class="tf-icons bx bx-edit"></i>
                </a>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteService({{ $s->id }})" title="Delete Service">
                  <i class="tf-icons bx bx-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="mt-3">{{ $services->links() }}</div>
    </div>
  </div>
</div>

<script>
// Delete service function
function deleteService(serviceId) {
    if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/services/${serviceId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection