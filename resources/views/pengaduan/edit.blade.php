@extends('layouts.admin')

@section('title', 'Edit Pengaduan: ' . $pengaduan->kode_tiket)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Edit Pengaduan</h5>
        <small class="text-muted">Kode: <code>{{ $pengaduan->kode_tiket }}</code></small>
    </div>
    <a href="{{ route('pengaduan.show', $pengaduan) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('pengaduan.update', $pengaduan) }}">
    @csrf
    @method('PUT')

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" class="form-select" required>
                        @foreach (['infrastruktur' => 'Infrastruktur', 'keamanan' => 'Keamanan', 'kebersihan' => 'Kebersihan', 'kesehatan' => 'Kesehatan', 'sosial' => 'Sosial', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('kategori', $pengaduan->kategori) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Prioritas <span class="text-danger">*</span></label>
                    <select name="prioritas" class="form-select" required>
                        @foreach (['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi'] as $v => $l)
                            <option value="{{ $v }}" {{ old('prioritas', $pengaduan->prioritas) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Judul <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control" value="{{ old('judul', $pengaduan->judul) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                    <textarea name="deskripsi" class="form-control" rows="5" required>{{ old('deskripsi', $pengaduan->deskripsi) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat Lokasi</label>
                    <input type="text" name="alamat_lokasi" class="form-control" value="{{ old('alamat_lokasi', $pengaduan->alamat_lokasi) }}">
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('pengaduan.show', $pengaduan) }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Update
            </button>
        </div>
    </div>
</form>

@endsection