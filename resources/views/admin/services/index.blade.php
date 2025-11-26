@extends('layouts.admin')

@section('title', 'Services')

@section('content')
<div class="container-xxl">
  <!-- Pending Services Alert -->
  @php
    $pendingCount = collect($services)->where('status', 'Pending')->count();
  @endphp
  
  @if($pendingCount > 0 && !request('status'))
  <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
    <i class="bx bx-time-five me-3 fs-4"></i>
    <div class="flex-grow-1">
      <h6 class="alert-heading mb-1">
        <i class="bx bx-bell me-1"></i>Pending Services Alert
      </h6>
      <p class="mb-0">
        You have <strong>{{ $pendingCount }}</strong> service(s) with <strong>Pending</strong> status that require attention.
      </p>
    </div>
    <div class="ms-3">
      <button class="btn btn-warning btn-sm" onclick="filterByStatus('Pending')">
        <i class="bx bx-filter me-1"></i>View Pending
      </button>
    </div>
  </div>
  @endif

  <div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
      <h5 class="mb-0">
        Services
        @if(request('status'))
          <span class="badge bg-warning ms-2">{{ request('status') }}</span>
        @endif
      </h5>
      <div class="d-flex flex-wrap gap-2 align-items-center">
        <!-- Status Filter -->
        <select class="form-select form-select-sm" style="width: 150px;" onchange="filterByStatus(this.value)">
          <option value="">All Status</option>
          <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
          <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
          <option value="Suspended" {{ request('status') == 'Suspended' ? 'selected' : '' }}>Suspended</option>
          <option value="Terminated" {{ request('status') == 'Terminated' ? 'selected' : '' }}>Terminated</option>
          <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
          <option value="Disuspen" {{ request('status') == 'Disuspen' ? 'selected' : '' }}>Disuspen</option>
          <option value="Sedang Dibuat" {{ request('status') == 'Sedang Dibuat' ? 'selected' : '' }}>Sedang Dibuat</option>
          <option value="Ditutup" {{ request('status') == 'Ditutup' ? 'selected' : '' }}>Ditutup</option>
        </select>
        
        <button type="button" class="btn btn-danger btn-sm" id="deleteSelectedBtn" style="display: none;" onclick="deleteSelected()">
          <i class="bx bx-trash"></i>
          <span class="d-none d-md-inline ms-1">Delete Selected</span>
          (<span id="selectedCount">0</span>)
        </button>
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-sm">
          <i class="bx bx-plus"></i>
          <span class="d-none d-sm-inline ms-1">New Service</span>
        </a>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead class="table-light">
            <tr>
              <th width="45" class="text-center">
                <input type="checkbox" class="form-check-input cursor-pointer" id="selectAll" onclick="toggleSelectAll()" style="width: 18px; height: 18px;">
              </th>
              <th>#</th>
              <th>Product</th>
              <th class="d-none d-md-table-cell">Domain</th>
              <th>Client</th>
              <th class="d-none d-lg-table-cell">Due Date</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($services as $s)
            <tr>
              <td class="text-center">
                <input type="checkbox" class="form-check-input row-checkbox cursor-pointer" value="{{ $s->id }}" onchange="updateSelectedCount()" style="width: 18px; height: 18px;">
              </td>
            <td>{{ $s->id }}</td>
              <td>{{ $s->product }}</td>
              <td class="d-none d-md-table-cell">{{ $s->domain ?? '-' }}</td>
              <td>{{ $s->client_name ?? '-' }}</td>
              <td class="d-none d-lg-table-cell">{{ $s->due_date ? date('Y-m-d', strtotime($s->due_date)) : '-' }}</td>
              <td>
                <span class="badge 
                  @switch($s->status)
                    @case('Active') bg-success @break
                    @case('Pending') bg-warning @break
                    @case('Sedang Dibuat') bg-primary @break
                    @case('Suspended') bg-secondary @break
                    @case('Terminated') bg-secondary @break
                    @case('Dibatalkan') bg-secondary @break
                    @case('Disuspen') bg-secondary @break
                    @case('Ditutup') bg-secondary @break
                    @default bg-secondary
                  @endswitch
                ">{{ $s->status }}</span>
              </td>
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
      </div>
    </div>
  </div>
</div>

<script>
// Toggle select all checkboxes
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateSelectedCount();
}

// Update selected count and show/hide delete button
function updateSelectedCount() {
    const checked = document.querySelectorAll('.row-checkbox:checked');
    const count = checked.length;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('deleteSelectedBtn').style.display = count > 0 ? 'inline-block' : 'none';
}

// Delete selected items
function deleteSelected() {
    const checked = document.querySelectorAll('.row-checkbox:checked');
    if (checked.length === 0) return;
    
    if (confirm(`Are you sure you want to delete ${checked.length} service(s)? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.services.bulk-delete") }}';
        form.innerHTML = `@csrf`;
        
        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

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

// Filter by status function
function filterByStatus(status) {
    const url = new URL(window.location);
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    window.location.href = url.toString();
}
</script>
@endsection