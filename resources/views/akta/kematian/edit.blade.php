@extends('layouts.admin')

@section('title', 'Edit Kematian: ' . ($kematian->warga->nama ?? '-'))

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Edit Data Kematian</h5>
    <a href="{{ route('kematian.show', $kematian) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('kematian.update', $kematian) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="alert alert-secondary">
                <strong>{{ $kematian->warga->nama ?? '-' }}</strong> — NIK {{ $kematian->warga->nik ?? '-' }}
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Meninggal</label>
                    <input type="date" name="tanggal_meninggal" class="form-control" value="{{ old('tanggal_meninggal', $kematian->tanggal_meninggal->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jam Meninggal</label>
                    <input type="time" name="jam_meninggal" class="form-control" value="{{ old('jam_meninggal', $kematian->jam_meninggal) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tempat Meninggal</label>
                    <input type="text" name="tempat_meninggal" class="form-control" value="{{ old('tempat_meninggal', $kematian->tempat_meninggal) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sebab</label>
                    <select name="sebab" class="form-select" required>
                        @foreach (['sakit' => 'Sakit', 'kecelakaan' => 'Kecelakaan', 'usia_lanjut' => 'Usia Lanjut', 'wabah' => 'Wabah', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('sebab', $kematian->sebab) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Keterangan Sebab</label>
                    <input type="text" name="keterangan_sebab" class="form-control" value="{{ old('keterangan_sebab', $kematian->keterangan_sebab) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tempat Pemakaman</label>
                    <input type="text" name="tempat_pemakaman" class="form-control" value="{{ old('tempat_pemakaman', $kematian->tempat_pemakaman) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pemakaman</label>
                    <input type="date" name="tanggal_pemakaman" class="form-control" value="{{ old('tanggal_pemakaman', $kematian->tanggal_pemakaman?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">No Akta</label>
                    <input type="text" name="no_akta_kematian" class="form-control" value="{{ old('no_akta_kematian', $kematian->no_akta_kematian) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Akta</label>
                    <input type="date" name="tanggal_akta" class="form-control" value="{{ old('tanggal_akta', $kematian->tanggal_akta?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ganti Dokumen</label>
                    <input type="file" name="dokumen_akta" class="form-control" accept=".pdf,image/*">
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $kematian->keterangan) }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('kematian.show', $kematian) }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Update</button>
        </div>
    </div>
</form>

@endsection