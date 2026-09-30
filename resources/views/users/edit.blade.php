@extends('layouts.admin')

@section('title', 'Edit Akun: ' . $user->name)

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #64748b; font-weight: 600;
        border: none; border-bottom: 3px solid transparent;
    }
    .nav-tabs .nav-link.active {
        color: #667eea; border-bottom: 3px solid #667eea; background: transparent;
    }
    .form-label { font-weight: 600; font-size: 14px; color: #334155; }
    .permission-group {
        border: 1px solid #e2e8f0; border-radius: 8px;
        padding: 12px 15px; margin-bottom: 10px; background: #f8fafc;
    }
    .permission-group h6 {
        font-weight: 700; color: #1e293b;
        margin-bottom: 10px; font-size: 14px; text-transform: capitalize;
    }
    .permission-group .form-check { display: inline-block; margin-right: 15px; margin-bottom: 5px; }
    .role-check {
        border: 2px solid #e2e8f0; border-radius: 8px;
        padding: 10px 15px; transition: all 0.2s; cursor: pointer;
    }
    .role-check:hover { border-color: #667eea; background: #f1f5ff; }
    .role-check input:checked ~ label { color: #667eea; font-weight: 700; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Edit Akun: {{ $user->name }}</h5>
        <small class="text-muted">NIK: {{ $user->nik }}</small>
    </div>
    <div>
        @can('user.reset_password')
        <form method="POST" action="{{ route('users.reset-password', $user) }}" class="d-inline"
              onsubmit="return confirm('Reset password {{ $user->name }}?')">
            @csrf
            <button type="submit" class="btn btn-outline-warning btn-sm">
                <i class="bi bi-key"></i> Reset Password
            </button>
        </form>
        @endcan
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <strong><i class="bi bi-exclamation-circle"></i> Ada kesalahan:</strong>
    <ul class="mb-0 mt-2">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('users.update', $user) }}">
    @csrf
    @method('PUT')

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-identitas">
                        <i class="bi bi-person-badge"></i> Identitas
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-kependudukan">
                        <i class="bi bi-house-door"></i> Kependudukan
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-jabatan">
                        <i class="bi bi-briefcase"></i> Jabatan
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-hak-akses">
                        <i class="bi bi-shield-lock"></i> Hak Akses
                    </button>
                </li>
            </ul>

            <div class="tab-content">

                {{-- ============ TAB 1: IDENTITAS ============ --}}
                <div class="tab-pane fade show active" id="tab-identitas">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">NIK</label>
                            <input type="text" class="form-control" value="{{ $user->nik }}" readonly disabled>
                            <small class="text-muted">NIK tidak dapat diubah</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No HP <span class="text-danger">*</span></label>
                            <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                                   value="{{ old('no_hp', $user->no_hp) }}" required>
                            @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password Baru</label>
                            <input type="text" name="password" class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Kosongkan jika tidak ingin ganti">
                            <small class="text-muted">Isi hanya kalau mau ganti password (min 8 karakter)</small>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status Akun <span class="text-danger">*</span></label>
                            <select name="status_akun" class="form-select" required>
                                <option value="aktif" {{ old('status_akun', $user->status_akun) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="pending" {{ old('status_akun', $user->status_akun) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="nonaktif" {{ old('status_akun', $user->status_akun) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ============ TAB 2: KEPENDUDUKAN ============ --}}
                <div class="tab-pane fade" id="tab-kependudukan">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">RT</label>
                            <select name="rt_id" class="form-select">
                                <option value="">-- Pilih RT --</option>
                                @foreach ($rts as $rt)
                                    <option value="{{ $rt->id }}" {{ old('rt_id', $user->rt_id) == $rt->id ? 'selected' : '' }}>
                                        {{ $rt->nama_rt }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status Kependudukan <span class="text-danger">*</span></label>
                            <select name="status_kependudukan" class="form-select" required>
                                <option value="warga" {{ old('status_kependudukan', $user->status_kependudukan) == 'warga' ? 'selected' : '' }}>Warga</option>
                                <option value="pendatang" {{ old('status_kependudukan', $user->status_kependudukan) == 'pendatang' ? 'selected' : '' }}>Pendatang</option>
                                <option value="kontrak" {{ old('status_kependudukan', $user->status_kependudukan) == 'kontrak' ? 'selected' : '' }}>Kontrak</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $user->alamat) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No KK</label>
                            <input type="text" name="no_kk" class="form-control" maxlength="16"
                                   value="{{ old('no_kk', $user->no_kk) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status dalam Keluarga <span class="text-danger">*</span></label>
                            <select name="status_keluarga" class="form-select" required>
                                @foreach (['kepala' => 'Kepala Keluarga', 'istri' => 'Istri', 'anak' => 'Anak', 'famili' => 'Famili', 'lainnya' => 'Lainnya'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('status_keluarga', $user->status_keluarga) == $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="L" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" class="form-control"
                                   value="{{ old('tgl_lahir', $user->tgl_lahir?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Agama</label>
                            <input type="text" name="agama" class="form-control" value="{{ old('agama', $user->agama) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control" value="{{ old('pekerjaan', $user->pekerjaan) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status Perkawinan</label>
                            <input type="text" name="status_kawin" class="form-control" value="{{ old('status_kawin', $user->status_kawin) }}">
                        </div>
                    </div>
                </div>

                {{-- ============ TAB 3: JABATAN ============ --}}
                <div class="tab-pane fade" id="tab-jabatan">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $user->jabatan) }}"
                                   placeholder="Contoh: Bendahara RW, Ketua RT 05">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Periode Jabatan — Mulai</label>
                            <input type="date" name="periode_jabatan_mulai" class="form-control"
                                   value="{{ old('periode_jabatan_mulai', $user->periode_jabatan_mulai?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Periode Jabatan — Selesai</label>
                            <input type="date" name="periode_jabatan_selesai" class="form-control"
                                   value="{{ old('periode_jabatan_selesai', $user->periode_jabatan_selesai?->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>

                {{-- ============ TAB 4: HAK AKSES ============ --}}
                <div class="tab-pane fade" id="tab-hak-akses">

                    <h6 class="fw-bold mb-3"><i class="bi bi-person-badge"></i> Role / Jabatan</h6>
                    <p class="text-muted small">Pilih role yang sesuai. Bisa pilih lebih dari satu.</p>
                    <div class="row g-2 mb-4">
                        @foreach ($roles as $role)
                            <div class="col-md-3 col-6">
                                <div class="role-check">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                           id="role-{{ $role->id }}" class="form-check-input"
                                           {{ in_array($role->name, old('roles', $userRoles)) ? 'checked' : '' }}>
                                    <label for="role-{{ $role->id }}" class="form-check-label ms-1" style="cursor: pointer;">
                                        {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <hr>

                    <h6 class="fw-bold mb-3 mt-4"><i class="bi bi-key"></i> Hak Akses Tambahan (Override)</h6>
                    <p class="text-muted small">
                        <i class="bi bi-info-circle"></i>
                        Centang untuk memberi akses tambahan di luar role.
                    </p>

                    @foreach ($permissions as $modul => $perms)
                        <div class="permission-group">
                            <h6><i class="bi bi-folder"></i> {{ ucwords(str_replace('_', ' ', $modul)) }}</h6>
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
                                           id="perm-{{ $perm->id }}" class="form-check-input"
                                           {{ in_array($perm->name, old('permissions', $userPermissions)) ? 'checked' : '' }}>
                                    <label for="perm-{{ $perm->id }}" class="form-check-label">{{ $labelAksi }}</label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                </div>

            </div>

        </div>

        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Update Akun
            </button>
        </div>
    </div>
</form>

@endsection