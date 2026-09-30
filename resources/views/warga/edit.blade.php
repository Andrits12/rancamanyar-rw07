@extends('layouts.admin')

@section('title', 'Edit Warga: ' . $warga->nama)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Edit Warga</h5>
        <small class="text-muted">NIK: {{ $warga->nik }}</small>
    </div>
    <a href="{{ route('warga.show', $warga) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('warga.update', $warga) }}">
    @csrf
    @method('PUT')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">NIK <span class="text-danger">*</span></label>
                    <input type="text" name="nik" class="form-control" maxlength="16" value="{{ old('nik', $warga->nik) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $warga->nama) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Keluarga <span class="text-danger">*</span></label>
                    <select name="keluarga_id" class="form-select" required>
                        @foreach ($keluargas as $kk)
                            <option value="{{ $kk->id }}" {{ old('keluarga_id', $warga->keluarga_id) == $kk->id ? 'selected' : '' }}>
                                {{ $kk->no_kk }} — {{ $kk->kepala_keluarga_nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">RT <span class="text-danger">*</span></label>
                    <select name="rt_id" class="form-select" required>
                        @foreach ($rts as $rt)
                            <option value="{{ $rt->id }}" {{ old('rt_id', $warga->rt_id) == $rt->id ? 'selected' : '' }}>{{ $rt->nama_rt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <option value="L" {{ old('jenis_kelamin', $warga->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin', $warga->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $warga->tempat_lahir) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir', $warga->tgl_lahir?->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status Keluarga</label>
                    <select name="status_keluarga" class="form-select" required>
                        @foreach (['kepala_keluarga' => 'Kepala Keluarga', 'istri' => 'Istri', 'anak' => 'Anak', 'menantu' => 'Menantu', 'cucu' => 'Cucu', 'orang_tua' => 'Orang Tua', 'mertua' => 'Mertua', 'famili_lain' => 'Famili Lain', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('status_keluarga', $warga->status_keluarga) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Agama</label>
                    <input type="text" name="agama" class="form-control" value="{{ old('agama', $warga->agama) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pendidikan</label>
                    <input type="text" name="pendidikan" class="form-control" value="{{ old('pendidikan', $warga->pendidikan) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pekerjaan</label>
                    <input type="text" name="pekerjaan" class="form-control" value="{{ old('pekerjaan', $warga->pekerjaan) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status Perkawinan</label>
                    <select name="status_kawin" class="form-select">
                        <option value="">-- Pilih --</option>
                        @foreach (['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $sk)
                            <option value="{{ $sk }}" {{ old('status_kawin', $warga->status_kawin) == $sk ? 'selected' : '' }}>{{ $sk }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kewarganegaraan</label>
                    <input type="text" name="kewarganegaraan" class="form-control" value="{{ old('kewarganegaraan', $warga->kewarganegaraan) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Golongan Darah</label>
                    <input type="text" name="golongan_darah" class="form-control" value="{{ old('golongan_darah', $warga->golongan_darah) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">No Akta Lahir</label>
                    <input type="text" name="no_akta_lahir" class="form-control" value="{{ old('no_akta_lahir', $warga->no_akta_lahir) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status Hidup <span class="text-danger">*</span></label>
                    <select name="status_hidup" class="form-select" required>
                        <option value="hidup" {{ old('status_hidup', $warga->status_hidup) == 'hidup' ? 'selected' : '' }}>Hidup</option>
                        <option value="meninggal" {{ old('status_hidup', $warga->status_hidup) == 'meninggal' ? 'selected' : '' }}>Meninggal</option>
                        <option value="pindah" {{ old('status_hidup', $warga->status_hidup) == 'pindah' ? 'selected' : '' }}>Pindah</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $warga->keterangan) }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('warga.show', $warga) }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Update Warga
            </button>
        </div>
    </div>
</form>

@endsection