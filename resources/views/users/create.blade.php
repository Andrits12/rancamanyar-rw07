@extends('layouts.admin')

@section('title', 'Buat Akun Baru')

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #64748b;
        font-weight: 600;
        border: none;
        border-bottom: 3px solid transparent;
    }
    .nav-tabs .nav-link.active {
        color: #667eea;
        border-bottom: 3px solid #667eea;
        background: transparent;
    }
    .form-label {
        font-weight: 600;
        font-size: 14px;
        color: #334155;
    }
    .form-label .text-danger { font-weight: 400; }
    .permission-group {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 15px;
        margin-bottom: 10px;
        background: #f8fafc;
    }
    .permission-group h6 {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 10px;
        font-size: 14px;
        text-transform: capitalize;
    }
    .permission-group .form-check {
        display: inline-block;
        margin-right: 15px;
        margin-bottom: 5px;
    }
    .role-check {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 15px;
        transition: all 0.2s;
        cursor: pointer;
    }
    .role-check:hover {
        border-color: #667eea;
        background: #f1f5ff;
    }
    .role-check input:checked ~ label {
        color: #667eea;
        font-weight: 700;
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Buat Akun Baru</h5>
        <small class="text-muted">Isi data di bawah ini untuk membuat akun warga</small>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
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

<form method="POST" action="{{ route('users.store') }}">
    @csrf

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            {{-- TABS NAVIGATION --}}
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

            {{-- TAB CONTENT --}}
            <div class="tab-content">

                {{-- ============ TAB 1: IDENTITAS ============ --}}
                <div class="tab-pane fade show active" id="tab-identitas">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">NIK <span class="text-danger">*</span></label>
                            <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror"
                                   value="{{ old('nik') }}" maxlength="16" pattern="[0-9]{16}" required>
                            <small class="text-muted">16 digit angka</small>
                            @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No HP <span class="text-danger">*</span></label>
                            <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                                   value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx" required>
                            <small class="text-muted">Digunakan untuk login</small>
                            @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}">
                            <small class="text-muted">Opsional, untuk reset password</small>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password Awal <span class="text-danger">*</span></label>
                            <input type="text" name="password" class="form-control @error('password') is-invalid @enderror"
                                   value="{{ old('password', 'Warga123!') }}" required minlength="8">
                            <small class="text-muted">Min 8 karakter. Warga wajib ganti saat login pertama.</small>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status Akun <span class="text-danger">*</span></label>
                            <select name="status_akun" class="form-select @error('status_akun') is-invalid @enderror" required>
                                <option value="aktif" {{ old('status_akun') == 'aktif' ? 'selected' : '' }}>Aktif (langsung bisa login)</option>
                                <option value="pending" {{ old('status_akun') == 'pending' ? 'selected' : '' }}>Pending (menunggu verifikasi)</option>
                                <option value="nonaktif" {{ old('status_akun') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            @error('status_akun') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- ============ TAB 2: KEPENDUDUKAN ============ --}}
                <div class="tab-pane fade" id="tab-kependudukan">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">RT <span class="text-danger">*</span></label>
                            <select name="rt_id" class="form-select @error('rt_id') is-invalid @enderror">
                                <option value="">-- Pilih RT --</option>
                                @foreach ($rts as $rt)
                                    <option value="{{ $rt->id }}" {{ old('rt_id') == $rt->id ? 'selected' : '' }}>
                                        {{ $rt->nama_rt }}
                                    </option>
                                @endforeach
                            </select>
                            @error('rt_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status Kependudukan <span class="text-danger">*</span></label>
                            <select name="status_kependudukan" class="form-select @error('status_kependudukan') is-invalid @enderror" required>
                                <option value="warga" {{ old('status_kependudukan') == 'warga' ? 'selected' : '' }}>Warga</option>
                                <option value="pendatang" {{ old('status_kependudukan') == 'pendatang' ? 'selected' : '' }}>Pendatang</option>
                                <option value="kontrak" {{ old('status_kependudukan') == 'kontrak' ? 'selected' : '' }}>Kontrak</option>
                            </select>
                            @error('status_kependudukan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Hubungkan ke Data Warga</label>
                            <select name="warga_id" class="form-select">
                                <option value="">-- Tidak dihubungkan --</option>
                                @foreach (\App\Models\Warga::whereNull('user_id')->with('keluarga')->orderBy('nama')->get() as $w)
                                <option value="{{ $w->id }}" {{ old('warga_id') == $w->id ? 'selected' : '' }}>
                                    {{ $w->nik }} — {{ $w->nama }} ({{ $w->keluarga->kepala_keluarga_nama ?? '-' }})
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                Kalau akun ini milik warga yang sudah terdata, pilih namanya di sini.
                            </small>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="2">{{ old('alamat') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No KK</label>
                            <input type="text" name="no_kk" class="form-control @error('no_kk') is-invalid @enderror"
                                   value="{{ old('no_kk') }}" maxlength="16" pattern="[0-9]{16}">
                            <small class="text-muted">16 digit angka (opsional)</small>
                            @error('no_kk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status dalam Keluarga <span class="text-danger">*</span></label>
                            <select name="status_keluarga" class="form-select @error('status_keluarga') is-invalid @enderror" required>
                                <option value="kepala" {{ old('status_keluarga') == 'kepala' ? 'selected' : '' }}>Kepala Keluarga</option>
                                <option value="istri" {{ old('status_keluarga') == 'istri' ? 'selected' : '' }}>Istri</option>
                                <option value="anak" {{ old('status_keluarga') == 'anak' ? 'selected' : '' }}>Anak</option>
                                <option value="famili" {{ old('status_keluarga') == 'famili' ? 'selected' : '' }}>Famili</option>
                                <option value="lainnya" {{ old('status_keluarga') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('status_keluarga') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Agama</label>
                            <input type="text" name="agama" class="form-control" value="{{ old('agama') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control" value="{{ old('pekerjaan') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status Perkawinan</label>
                            <input type="text" name="status_kawin" class="form-control" value="{{ old('status_kawin') }}"
                                   placeholder="Belum Kawin / Kawin / Cerai">
                        </div>
                    </div>
                </div>

                {{-- ============ TAB 3: JABATAN ============ --}}
                <div class="tab-pane fade" id="tab-jabatan">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Kosongkan jika warga tidak memiliki jabatan khusus. Isi jika warga menjabat sebagai
                        Ketua RT, Bendahara, Sekretaris, dll.
                    </div>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan') }}"
                                   placeholder="Contoh: Bendahara RW, Ketua RT 05, Sekretaris">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Periode Jabatan — Mulai</label>
                            <input type="date" name="periode_jabatan_mulai" class="form-control"
                                   value="{{ old('periode_jabatan_mulai') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Periode Jabatan — Selesai</label>
                            <input type="date" name="periode_jabatan_selesai" class="form-control"
                                   value="{{ old('periode_jabatan_selesai') }}">
                        </div>
                    </div>
                </div>

                {{-- ============ TAB 4: HAK AKSES ============ --}}
                <div class="tab-pane fade" id="tab-hak-akses">

                    {{-- ROLE --}}
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-person-badge"></i> Role / Jabatan
                    </h6>
                    <p class="text-muted small">
                        Pilih role yang sesuai. Role menentukan hak akses dasar akun ini.
                        Bisa pilih lebih dari satu.
                    </p>
                    <div class="row g-2 mb-4">
                        @foreach ($roles as $role)
                            <div class="col-md-3 col-6">
                                <div class="role-check">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                           id="role-{{ $role->id }}" class="form-check-input"
                                           {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                                    <label for="role-{{ $role->id }}" class="form-check-label ms-1" style="cursor: pointer;">
                                        {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <hr>

                    {{-- PERMISSION OVERRIDE --}}
                    <h6 class="fw-bold mb-3 mt-4">
                        <i class="bi bi-key"></i> Hak Akses Tambahan (Override)
                    </h6>
                    <p class="text-muted small">
                        <i class="bi bi-info-circle"></i>
                        Centang untuk memberi akses tambahan di luar role. Biarkan kosong jika cukup pakai role saja.
                    </p>

                    @foreach ($permissions as $modul => $perms)
                        <div class="permission-group">
                            <h6>
                                <i class="bi bi-folder"></i> {{ ucwords(str_replace('_', ' ', $modul)) }}
                            </h6>
                            @foreach ($perms as $perm)
                                @php
                                    $aksi = explode('.', $perm->name)[1] ?? $perm->name;
                                    $labelAksi = [
                                        'view' => 'Lihat',
                                        'create' => 'Tambah',
                                        'edit' => 'Edit',
                                        'delete' => 'Hapus',
                                        'export' => 'Export',
                                        'approve' => 'Setujui',
                                        'sign' => 'Tanda Tangan',
                                        'handle' => 'Tangani',
                                        'schedule' => 'Atur Jadwal',
                                        'pinjam' => 'Pinjam',
                                        'verifikasi' => 'Verifikasi',
                                        'lamar' => 'Lamar',
                                        'reset_password' => 'Reset Password',
                                        'view_rw' => 'Dashboard RW',
                                        'view_rt' => 'Dashboard RT',
                                        'view_keuangan' => 'Dashboard Keuangan',
                                        'view_security' => 'Dashboard Security',
                                    ][$aksi] ?? ucwords($aksi);
                                @endphp
                                <div class="form-check">
                                    <input type="checkbox" name="permissions[]"
                                           value="{{ $perm->name }}"
                                           id="perm-{{ $perm->id }}"
                                           class="form-check-input"
                                           {{ in_array($perm->name, old('permissions', [])) ? 'checked' : '' }}>
                                    <label for="perm-{{ $perm->id }}" class="form-check-label">
                                        {{ $labelAksi }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                </div>

            </div>
            {{-- END TAB CONTENT --}}

        </div>

        {{-- FOOTER BUTTONS --}}
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Simpan Akun
            </button>
        </div>
    </div>
</form>

@endsection