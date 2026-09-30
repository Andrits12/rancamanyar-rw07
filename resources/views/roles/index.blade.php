@extends('layouts.admin')

@section('title', 'Role & Permission')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Role & Permission</h5>
        <small class="text-muted">Total: {{ $roles->count() }} role</small>
    </div>
    @can('role.create')
    <a href="{{ route('roles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Buat Role Baru
    </a>
    @endcan
</div>

<div class="row g-3">
    @foreach ($roles as $role)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-shield-check text-primary"></i>
                        {{ ucwords(str_replace('_', ' ', $role->name)) }}
                    </h6>
                    @if (in_array($role->name, ['super_admin', 'ketua_rw', 'ketua_rt', 'sekretaris', 'bendahara', 'sesepuh', 'security', 'posyandu', 'warga']))
                        <span class="badge bg-secondary">Bawaan</span>
                    @else
                        <span class="badge bg-success">Custom</span>
                    @endif
                </div>

                <div class="text-muted small mb-3">
                    <div><i class="bi bi-people"></i> {{ $role->users_count }} user</div>
                    <div><i class="bi bi-key"></i> {{ $role->permissions_count }} permission</div>
                </div>

                <div class="d-flex gap-1">
                    @can('role.edit')
                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    @endcan
                    @can('role.view')
                    <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-eye"></i> Detail
                    </a>
                    @endcan
                    @can('role.delete')
                    @if (!in_array($role->name, ['super_admin', 'ketua_rw', 'ketua_rt', 'sekretaris', 'bendahara', 'sesepuh', 'security', 'posyandu', 'warga']))
                    <form method="POST" action="{{ route('roles.destroy', $role) }}" class="d-inline"
                          onsubmit="return confirm('Yakin hapus role {{ $role->name }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                    @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection