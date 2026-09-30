@extends('layouts.admin')

@section('title', 'Catat Kelahiran')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Catat Kelahiran</h5>
    <a href="{{ route('kelahiran.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('kelahiran.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold mb-3 text-primary">👶 Data Bayi</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Bayi <span class="text-danger">*</span></label>
                    <input type="text" name="nama_bayi" class="form-control" value="{{ old('nama_bayi') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIK Bayi (opsional)</label>
                    <input type="text" name="nik_bayi" class="form-control" value="{{ old('nik_bayi') }}" 
                        maxlength="16" placeholder="Kosongkan untuk auto-generate">
                    <small class="text-muted">Kalau sudah punya NIK, isi di sini</small>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>👦 Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>👧 Perempuan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kondisi Lahir <span class="text-danger">*</span></label>
                    <select name="kondisi_lahir" class="form-select" required>
                        @foreach (['normal' => 'Normal', 'prematur' => 'Prematur', 'cacat' => 'Cacat', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('kondisi_lahir', 'normal') == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jam Lahir</label>
                    <input type="time" name="jam_lahir" class="form-control" value="{{ old('jam_lahir') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Berat (kg)</label>
                    <input type="number" step="0.01" name="berat_lahir" class="form-control" value="{{ old('berat_lahir') }}" min="0" max="10">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Panjang (cm)</label>
                    <input type="number" step="0.01" name="panjang_lahir" class="form-control" value="{{ old('panjang_lahir') }}" min="0" max="100">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}"
                           placeholder="RS / Klinik / Rumah">
                </div>
                <div class="col-md-6">
                    <label class="form-label">RT <span class="text-danger">*</span></label>
                    <select name="rt_id" class="form-select" required>
                        <option value="">-- Pilih RT --</option>
                        @foreach ($rts as $rt)
                            <option value="{{ $rt->id }}" {{ old('rt_id', auth()->user()->rt_id) == $rt->id ? 'selected' : '' }}>{{ $rt->nama_rt }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-primary">👨‍👩‍👧 Data Orang Tua</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Ayah <span class="text-danger">*</span></label>
                    <input type="text" name="nama_ayah" class="form-control" value="{{ old('nama_ayah') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIK Ayah</label>
                    <input type="text" name="nik_ayah" class="form-control" value="{{ old('nik_ayah') }}" maxlength="16">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Ibu <span class="text-danger">*</span></label>
                    <input type="text" name="nama_ibu" class="form-control" value="{{ old('nama_ibu') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIK Ibu</label>
                    <input type="text" name="nik_ibu" class="form-control" value="{{ old('nik_ibu') }}" maxlength="16">
                </div>
                <div class="col-12">
                    <label class="form-label">Keluarga (KK) <span class="text-danger">*</span></label>
                    <select name="keluarga_id" class="form-select" required>
                        <option value="">-- Tidak terkait KK --</option>
                        @foreach ($keluargas as $kk)
                            <option value="{{ $kk->id }}" {{ old('keluarga_id') == $kk->id ? 'selected' : '' }}>
                                {{ $kk->no_kk }} — {{ $kk->kepala_keluarga_nama }} ({{ $kk->rt->nama_rt ?? '-' }})
                            </option>
                        @endforeach
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="tambah_ke_warga" value="1" class="form-check-input" 
                                    id="tambah_ke_warga" {{ old('tambah_ke_warga', true) ? 'checked' : '' }}>
                                <label for="tambah_ke_warga" class="form-check-label">
                                    <i class="bi bi-person-plus text-success"></i>
                                    <strong>Otomatis tambahkan bayi ke Data Warga</strong> sebagai anggota keluarga
                                </label>
                            </div>
                        </div>
                    </select>
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-primary">📄 Akta & Dokumen</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">No Akta Kelahiran</label>
                    <input type="text" name="no_akta_kelahiran" class="form-control" value="{{ old('no_akta_kelahiran') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Akta</label>
                    <input type="date" name="tanggal_akta" class="form-control" value="{{ old('tanggal_akta') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Upload Dokumen Akta</label>
                    <input type="file" name="dokumen_akta" class="form-control" accept=".pdf,image/*">
                    <small class="text-muted">PDF/JPG/PNG, max 2MB</small>
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('kelahiran.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan</button>
        </div>
    </div>
</form>

@endsection