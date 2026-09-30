@extends('layouts.admin')

@section('title', 'Buat Role Baru')

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
        <h5 class="mb-1">Buat Role Baru</h5>
        <small class="text-muted">Buat role custom dengan permission tertentu</small>
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

<form method="POST" action="{{ route('roles.store') }}">
    @csrf

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="mb-4">
                <label class="form-label fw-bold">Nama Role <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="contoh: sie_kerohanian" required>
                <small class="text-muted">Gunakan huruf kecil dan underscore (_), tanpa spasi.</small>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <hr>

            <h6 class="fw-bold mb-3"><i class="bi bi-key"></i> Permission</h6>
            <p class="text-muted small">
                Centang permission yang ingin diberikan ke role ini.
                <a href="#" class="select-all-btn" onclick="toggleAll(event)">Centang semua</a> /
                <a href="#" class="select-all-btn" onclick="uncheckAll(event)">Kosongkan</a>
            </p>

            @foreach ($permissions as $modul => $perms)
                <div class="permission-group">
                    <h6>
                        <i class="bi bi-folder"></i> {{ ucwords(str_replace('_', ' ', $modul)) }}
                        <a href="#" class="select-all-btn float-end" onclick="toggleModul(event, '{{ $modul }}')">
                            pilih semua
                        </a>
                    </h6>
                    @foreach ($perms as $perm)
                        @php
                            $aksi = explode('.', $perm->name)[1] ?? $perm->name;
                            $labelAksi = [
                                'view' => 'Lihat', 'create' => 'Tambah', 'edit' => 'Edit',
                                'delete' => 'Hapus', 'export' => 'Export', 'approve' => 'Setujui',
                                'sign' => 'Tanda Tangan', 'handle' => 'Tangani', 'schedule' => 'Atur Jadwal',
                                'pinjam' => 'Pinjam', 'verifikasi' => 'Verifikasi', 'lamar' => 'Lamar',
                                'reset_password' => 'Reset Password',
                                'view_rw' => 'Dashboard RW', 'view_rt' => 'Dashboard RT',
                                'view_keuangan' => 'Dashboard Keuangan', 'view_security' => 'Dashboard Security',
                            ][$aksi] ?? ucwords($aksi);
                        @endphp
                        <div class="form-check">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                   id="perm-{{ $perm->id }}" class="form-check-input perm-{{ $modul }}"
                                   {{ in_array($perm->name, old('permissions', [])) ? 'checked' : '' }}>
                            <label for="perm-{{ $perm->id }}" class="form-check-label">{{ $labelAksi }}</label>
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
                <i class="bi bi-check-circle"></i> Simpan Role
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    function toggleAll(e) {
        e.preventDefault();
        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = true);
    }
    function uncheckAll(e) {
        e.preventDefault();
        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);
    }
    function toggleModul(e, modul) {
        e.preventDefault();
        const checkboxes = document.querySelectorAll('.perm-' + modul);
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
    }
</script>
@endpush

@endsection