@extends('layouts.admin')

@section('title', 'Tambah Warga')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Tambah Warga</h5>
        <small class="text-muted">Tambah anggota keluarga baru</small>
    </div>
    <a href="{{ $keluarga ? route('keluarga.show', $keluarga) : route('warga.index') }}" class="btn btn-outline-secondary btn-sm">
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

<form method="POST" action="{{ route('warga.store') }}">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">NIK <span class="text-danger">*</span></label>
                    <input type="text" name="nik" class="form-control" maxlength="16" pattern="[0-9]{16}"
                           value="{{ old('nik') }}" required>
                    <small class="text-muted">16 digit angka</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Keluarga (KK) <span class="text-danger">*</span></label>
                    <select name="keluarga_id" class="form-select" required>
                        <option value="">-- Pilih Keluarga --</option>
                        @foreach ($keluargas as $kk)
                            <option value="{{ $kk->id }}"
                                    data-rt="{{ $kk->rt_id }}"
                                    {{ old('keluarga_id', $keluarga->id ?? '') == $kk->id ? 'selected' : '' }}>
                                {{ $kk->no_kk }} — {{ $kk->kepala_keluarga_nama }} ({{ $kk->rt->nama_rt ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">RT <span class="text-danger">*</span></label>
                    <select name="rt_id" class="form-select" required>
                        <option value="">-- Pilih RT --</option>
                        @foreach ($rts as $rt)
                            <option value="{{ $rt->id }}"
                                    {{ old('rt_id', $keluarga->rt_id ?? '') == $rt->id ? 'selected' : '' }}>
                                {{ $rt->nama_rt }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                    <input type="date" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status Keluarga <span class="text-danger">*</span></label>
                    <select name="status_keluarga" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        @foreach (['kepala_keluarga' => 'Kepala Keluarga', 'istri' => 'Istri', 'anak' => 'Anak', 'menantu' => 'Menantu', 'cucu' => 'Cucu', 'orang_tua' => 'Orang Tua', 'mertua' => 'Mertua', 'famili_lain' => 'Famili Lain', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('status_keluarga') == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Agama</label>
                    <input type="text" name="agama" class="form-control" value="{{ old('agama') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pendidikan</label>
                    <input type="text" name="pendidikan" class="form-control" value="{{ old('pendidikan') }}"
                           placeholder="SD/SMP/SMA/S1/...">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pekerjaan</label>
                    <input type="text" name="pekerjaan" class="form-control" value="{{ old('pekerjaan') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status Perkawinan</label>
                    <select name="status_kawin" class="form-select">
                        <option value="">-- Pilih --</option>
                        @foreach (['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $sk)
                            <option value="{{ $sk }}" {{ old('status_kawin') == $sk ? 'selected' : '' }}>{{ $sk }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kewarganegaraan</label>
                    <input type="text" name="kewarganegaraan" class="form-control" value="{{ old('kewarganegaraan', 'WNI') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Golongan Darah</label>
                    <input type="text" name="golongan_darah" class="form-control" value="{{ old('golongan_darah') }}"
                           placeholder="A/B/AB/O">
                </div>
                <div class="col-md-4">
                    <label class="form-label">No Akta Lahir</label>
                    <input type="text" name="no_akta_lahir" class="form-control" value="{{ old('no_akta_lahir') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ $keluarga ? route('keluarga.show', $keluarga) : route('warga.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Simpan Warga
            </button>
        </div>
    </div>
</form>

@endsection