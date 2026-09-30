@extends('layouts.admin')

@section('title', 'Detail Role: ' . $role->name)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">{{ ucwords(str_replace('_', ' ', $role->name)) }}</h5>
        <small class="text-muted">{{ $role->permissions->count() }} permission • {{ $role->users->count() }} user</small>
    </div>
    <div>
        @can('role.edit')
        <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endcan
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong><i class="bi bi-key"></i> Permission</strong></div>
            <div class="card-body">
                @php
                    $grouped = $role->permissions->groupBy(fn($p) => explode('.', $p->name)[0]);
                @endphp
                @forelse ($grouped as $modul => $perms)
                    <div class="mb-3">
                        <h6 class="fw-bold text-primary">{{ ucwords(str_replace('_', ' ', $modul)) }}</h6>
                        @foreach ($perms as $perm)
                            <span class="badge bg-light text-dark border me-1 mb-1">
                                {{ explode('.', $perm->name)[1] ?? $perm->name }}
                            </span>
                        @endforeach
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada permission.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong><i class="bi bi-people"></i> User dengan Role Ini</strong></div>
            <div class="card-body">
                @forelse ($role->users as $user)
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                             style="width: 32px; height: 32px; font-weight: 700;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-semibold small">{{ $user->name }}</div>
                            <small class="text-muted">{{ $user->no_hp }}</small>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0 small">Belum ada user dengan role ini.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection23