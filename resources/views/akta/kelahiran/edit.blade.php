@extends('layouts.admin')

@section('title', 'Edit Kelahiran: ' . $kelahiran->nama_bayi)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Edit Kelahiran</h5>
    <a href="{{ route('kelahiran.show', $kelahiran) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('kelahiran.update', $kelahiran) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold mb-3 text-primary">👶 Data Bayi</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Bayi <span class="text-danger">*</span></label>
                    <input type="text" name="nama_bayi" class="form-control" value="{{ old('nama_bayi', $kelahiran->nama_bayi) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <option value="L" {{ old('jenis_kelamin', $kelahiran->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin', $kelahiran->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kondisi</label>
                    <select name="kondisi_lahir" class="form-select" required>
                        @foreach (['normal' => 'Normal', 'prematur' => 'Prematur', 'cacat' => 'Cacat', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('kondisi_lahir', $kelahiran->kondisi_lahir) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $kelahiran->tanggal_lahir->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jam Lahir</label>
                    <input type="time" name="jam_lahir" class="form-control" value="{{ old('jam_lahir', $kelahiran->jam_lahir) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Berat (kg)</label>
                    <input type="number" step="0.01" name="berat_lahir" class="form-control" value="{{ old('berat_lahir', $kelahiran->berat_lahir) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Panjang (cm)</label>
                    <input type="number" step="0.01" name="panjang_lahir" class="form-control" value="{{ old('panjang_lahir', $kelahiran->panjang_lahir) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $kelahiran->tempat_lahir) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">RT</label>
                    <select name="rt_id" class="form-select" required>
                        @foreach ($rts as $rt)
                            <option value="{{ $rt->id }}" {{ old('rt_id', $kelahiran->rt_id) == $rt->id ? 'selected' : '' }}>{{ $rt->nama_rt }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-primary">👨‍👩‍👧 Data Orang Tua</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Ayah</label>
                    <input type="text" name="nama_ayah" class="form-control" value="{{ old('nama_ayah', $kelahiran->nama_ayah) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIK Ayah</label>
                    <input type="text" name="nik_ayah" class="form-control" value="{{ old('nik_ayah', $kelahiran->nik_ayah) }}" maxlength="16">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Ibu</label>
                    <input type="text" name="nama_ibu" class="form-control" value="{{ old('nama_ibu', $kelahiran->nama_ibu) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIK Ibu</label>
                    <input type="text" name="nik_ibu" class="form-control" value="{{ old('nik_ibu', $kelahiran->nik_ibu) }}" maxlength="16">
                </div>
                <div class="col-12">
                    <label class="form-label">Keluarga (KK)</label>
                    <select name="keluarga_id" class="form-select">
                        <option value="">-- Tidak terkait KK --</option>
                        @foreach ($keluargas as $kk)
                            <option value="{{ $kk->id }}" {{ old('kelahiran_id', $kelahiran->keluarga_id) == $kk->id ? 'selected' : '' }}>
                                {{ $kk->no_kk }} — {{ $kk->kepala_keluarga_nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-primary">📄 Akta</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">No Akta</label>
                    <input type="text" name="no_akta_kelahiran" class="form-control" value="{{ old('no_akta_kelahiran', $kelahiran->no_akta_kelahiran) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Akta</label>
                    <input type="date" name="tanggal_akta" class="form-control" value="{{ old('tanggal_akta', $kelahiran->tanggal_akta?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ganti Dokumen</label>
                    <input type="file" name="dokumen_akta" class="form-control" accept=".pdf,image/*">
                    @if ($kelahiran->dokumen_akta)
                        <small class="text-muted">Dokumen sudah ada. Upload baru untuk ganti.</small>
                    @endif
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $kelahiran->keterangan) }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('kelahiran.show', $kelahiran) }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Update</button>
        </div>
    </div>
</form>

@endsection