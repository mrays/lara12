@extends('layouts.admin')

@section('title', 'Domain Extensions - Admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0">
                                <i class="bx bx-globe me-2"></i>Domain Extensions
                            </h5>
                        </div>
                        <div class="col-md-6 text-md-end mt-2 mt-md-0">
                            <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-md-end">
                                <button type="button" class="btn btn-danger btn-sm" id="deleteSelectedBtn" style="display: none;" onclick="deleteSelected()">
                                    <i class="bx bx-trash"></i>
                                    <span class="d-none d-md-inline ms-1">Delete Selected</span>
                                    (<span id="selectedCount">0</span>)
                                </button>
                                <a href="{{ route('admin.domain-extensions.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bx bx-plus"></i>
                                    <span class="d-none d-sm-inline ms-1">Extension Baru</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('admin.domain-extensions.index') }}" class="row g-2">
                                <div class="col-md-3">
                                    <select name="extension" class="form-select">
                                        <option value="">Semua Extension</option>
                                        @foreach($extensions as $ext)
                                            <option value="{{ $ext }}" {{ request('extension') == $ext ? 'selected' : '' }}>
                                                {{ $ext }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="duration" class="form-select">
                                        <option value="">Semua Durasi</option>
                                        @foreach($durations as $duration)
                                            <option value="{{ $duration }}" {{ request('duration') == $duration ? 'selected' : '' }}>
                                                {{ $duration }} Tahun
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <div class="btn-group w-100">
                                        <button type="submit" class="btn btn-outline-primary">
                                            <i class="bx bx-filter me-1"></i>Filter
                                        </button>
                                        <a href="{{ route('admin.domain-extensions.index') }}" class="btn btn-outline-secondary">
                                            <i class="bx bx-reset me-1"></i>Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="45" class="text-center">
                                        <input type="checkbox" class="form-check-input cursor-pointer" id="selectAll" onclick="toggleSelectAll()" style="width: 18px; height: 18px;">
                                    </th>
                                    <th>Extension</th>
                                    <th>Durasi</th>
                                    <th>Harga</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($domainExtensions as $extension)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input row-checkbox cursor-pointer" value="{{ $extension->id }}" onchange="updateSelectedCount()" style="width: 18px; height: 18px;">
                                    </td>
                                    <td>
                                        <span class="badge bg-info fs-6">{{ $extension->extension }}</span>
                                    </td>
                                    <td>{{ $extension->duration_display }}</td>
                                    <td class="fw-semibold">{{ $extension->formatted_price }}</td>
                                    <td>{{ $extension->description ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $extension->is_active ? 'success' : 'secondary' }}">
                                            {{ $extension->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-horizontal-rounded"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route('admin.domain-extensions.edit', $extension) }}" class="dropdown-item">
                                                        <i class="bx bx-edit me-1"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form method="POST" action="{{ route('admin.domain-extensions.toggle-status', $extension) }}" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bx bx-power-off me-1"></i> 
                                                            {{ $extension->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="POST" action="{{ route('admin.domain-extensions.destroy', $extension) }}" 
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus extension {{ $extension->extension }} ({{ $extension->duration_years }} tahun)?')" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bx bx-trash me-1"></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bx bx-globe fs-1"></i>
                                            <p class="mt-2 mb-0">Belum ada data extension domain</p>
                                            <small>Tambahkan extension domain baru untuk memulai</small>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.row-checkbox:checked');
    const count = checked.length;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('deleteSelectedBtn').style.display = count > 0 ? 'inline-block' : 'none';
}

function deleteSelected() {
    const checked = document.querySelectorAll('.row-checkbox:checked');
    if (checked.length === 0) return;
    
    if (confirm(`Apakah Anda yakin ingin menghapus ${checked.length} extension? Tindakan ini tidak dapat dibatalkan.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.domain-extensions.bulk-delete") }}';
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
</script>
@endsection
