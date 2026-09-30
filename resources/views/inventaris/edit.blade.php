@extends('layouts.admin')

@section('title', 'Edit Aset: ' . $aset->nama)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Edit Aset: {{ $aset->nama }}</h5>
    <a href="{{ route('inventaris.show', $aset) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('inventaris.update', $aset) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nama Aset <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $aset->nama) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" class="form-select" required>
                        @foreach (['tenda' => 'Tenda', 'kursi' => 'Kursi', 'meja' => 'Meja', 'elektronik' => 'Elektronik', 'alat_kerja' => 'Alat Kerja', 'perlengkapan' => 'Perlengkapan', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('kategori', $aset->kategori) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jumlah Total <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah_total" class="form-control" value="{{ old('jumlah_total', $aset->jumlah_total) }}" min="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Satuan <span class="text-danger">*</span></label>
                    <input type="text" name="satuan" class="form-control" value="{{ old('satuan', $aset->satuan) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                    <select name="kondisi" class="form-select" required>
                        @foreach (['baik' => 'Baik', 'rusak_ringan' => 'Rusak Ringan', 'rusak_berat' => 'Rusak Berat', 'hilang' => 'Hilang'] as $v => $l)
                            <option value="{{ $v }}" {{ old('kondisi', $aset->kondisi) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi', $aset->deskripsi) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Lokasi Penyimpanan</label>
                    <input type="text" name="lokasi_penyimpanan" class="form-control" value="{{ old('lokasi_penyimpanan', $aset->lokasi_penyimpanan) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Foto Aset</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                    @if ($aset->foto_url)
                        <img src="{{ $aset->foto_url }}" style="max-width: 150px; border-radius: 5px;" class="mt-2">
                    @endif
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Perolehan</label>
                    <input type="date" name="tanggal_perolehan" class="form-control" value="{{ old('tanggal_perolehan', $aset->tanggal_perolehan?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Harga Perolehan</label>
                    <input type="number" name="harga_perolehan" class="form-control" value="{{ old('harga_perolehan', $aset->harga_perolehan) }}" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sumber Perolehan</label>
                    <input type="text" name="sumber_perolehan" class="form-control" value="{{ old('sumber_perolehan', $aset->sumber_perolehan) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $aset->keterangan) }}</textarea>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                               {{ old('is_active', $aset->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">Aset Aktif (bisa dipinjam)</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('inventaris.show', $aset) }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Update</button>
        </div>
    </div>
</form>

@endsection