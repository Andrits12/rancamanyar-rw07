@extends('layouts.admin')

@section('title', 'Edit Role: ' . $role->name)

@push('styles')
<style>
    .permission-group {
        border: 1px solid #e2e8f0; border-radius: 8px;
        padding: 12px 15px; margin-bottom: 10px; background: #f8fafc;
    }
    .permission-group h6 {
        font-weight: 700; color: #1e293b;
        margin-bottom: 10px; font-size: 14px; text-transform: capitalize;
    }
    .permission-group .form-check { display: inline-block; margin-right: 15px; margin-bottom: 5px; }
    .select-all-btn { font-size: 12px; cursor: pointer; color: #667eea; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Edit Role: {{ ucwords(str_replace('_', ' ', $role->name)) }}</h5>
        <small class="text-muted">Ubah nama role atau permission yang diberikan</small>
    </div>
    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <strong>Ada kesalahan:</strong>
    <ul class="mb-0 mt-2">
        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
    </ul>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger">
    <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

<form method="POST" action="{{ route('roles.update', $role) }}">
    @csrf
    @method('PUT')

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="mb-4">
                <label class="form-label fw-bold">Nama Role <span class="text-danger">*</span></label>
                @php
                    $isProtected = in_array($role->name, [
                        'super_admin', 'ketua_rw', 'ketua_rt', 'sekretaris',
                        'bendahara', 'sesepuh', 'security', 'posyandu', 'warga'
                    ]);
                @endphp

                @if ($isProtected)
                    <input type="text" class="form-control" value="{{ $role->name }}" readonly disabled>
                    <input type="hidden" name="name" value="{{ $role->name }}">
                    <small class="text-muted">
                        <i class="bi bi-lock"></i> Nama role bawaan tidak dapat diubah.
                    </small>
                @else
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $role->name) }}"
                           placeholder="contoh: sie_kerohanian" required>
                    <small class="text-muted">Gunakan huruf kecil dan underscore (_), tanpa spasi.</small>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @endif
            </div>

            <hr>

            <h6 class="fw-bold mb-3"><i class="bi bi-key"></i> Permission</h6>

            @if ($role->name === 'super_admin')
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Role <strong>Super Admin</strong> otomatis memiliki semua permission.
                    Daftar di bawah hanya sebagai tampilan.
                </div>
            @else
                <p class="text-muted small">
                    Centang permission yang ingin diberikan ke role ini.
                    <a href="#" class="select-all-btn" onclick="toggleAll(event)">Centang semua</a> /
                    <a href="#" class="select-all-btn" onclick="uncheckAll(event)">Kosongkan</a>
                </p>
            @endif

            @foreach ($permissions as $modul => $perms)
                <div class="permission-group">
                    <h6>
                        <i class="bi bi-folder"></i> {{ ucwords(str_replace('_', ' ', $modul)) }}
                        @if ($role->name !== 'super_admin')
                        <a href="#" class="select-all-btn float-end"
                           onclick="toggleModul(event, '{{ $modul }}')">
                            pilih semua
                        </a>
                        @endif
                    </h6>
                    @foreach ($perms as $perm)
                        @php
                            $aksi = explode('.', $perm->name)[1] ?? $perm->name;
                            $labelAksi = [
                                'view' => 'Lihat', 'create' => 'Tambah', 'edit' => 'Edit',
                                'delete' => 'Hapus', 'export' => 'Export', 'approve' => 'Setujui',
                                'sign' => 'Tanda Tangan', 'handle' => 'Tangani',
                                'schedule' => 'Atur Jadwal', 'pinjam' => 'Pinjam',
                                'verifikasi' => 'Verifikasi', 'lamar' => 'Lamar',
                                'reset_password' => 'Reset Password',
                                'view_rw' => 'Dashboard RW', 'view_rt' => 'Dashboard RT',
                                'view_keuangan' => 'Dashboard Keuangan',
                                'view_security' => 'Dashboard Security',
                            ][$aksi] ?? ucwords($aksi);
                        @endphp
                        <div class="form-check">
                            <input type="checkbox"
                                   name="permissions[]"
                                   value="{{ $perm->name }}"
                                   id="perm-{{ $perm->id }}"
                                   class="form-check-input perm-{{ $modul }}"
                                   {{ in_array($perm->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                   {{ $role->name === 'super_admin' ? 'checked disabled' : '' }}>
                            <label for="perm-{{ $perm->id }}" class="form-check-label">
                                {{ $labelAksi }}
                            </label>
                        </div>
                    @endforeach
                </div>
            @endforeach

        </div>

        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Update Role
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    function toggleAll(e) {
        e.preventDefault();
        document.querySelectorAll('input[name="permissions[]"]:not(:disabled)')
            .forEach(cb => cb.checked = true);
    }
    function uncheckAll(e) {
        e.preventDefault();
        document.querySelectorAll('input[name="permissions[]"]:not(:disabled)')
            .forEach(cb => cb.checked = false);
    }
    function toggleModul(e, modul) {
        e.preventDefault();
        const checkboxes = document.querySelectorAll('.perm-' + modul + ':not(:disabled)');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
    }
</script>
@endpush

@endsection