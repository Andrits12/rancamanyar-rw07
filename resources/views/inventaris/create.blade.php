@extends('layouts.admin')

@section('title', 'Tambah Aset')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Tambah Aset Inventaris</h5>
    <a href="{{ route('inventaris.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('inventaris.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Pemilik Aset <span class="text-danger">*</span></label>
                    <select name="pemilik" id="pemilik" class="form-select" required onchange="toggleRt()">
                        <option value="rw" {{ old('pemilik') == 'rw' ? 'selected' : '' }}>RW 07 (Aset RW)</option>
                        <option value="rt" {{ old('pemilik', auth()->user()->hasRole('ketua_rt') && !auth()->user()->hasAnyRole(['super_admin','ketua_rw','sekretaris']) ? 'rt' : '') == 'rt' ? 'selected' : '' }}>RT (Aset RT)</option>
                    </select>
                </div>
            
                <div class="col-md-6" id="rt-wrapper" style="display: none;">
                    <label class="form-label">Pilih RT <span class="text-danger">*</span></label>
                    <select name="rt_id" id="rt_id" class="form-select">
                        <option value="">-- Pilih RT --</option>
                        @foreach (\App\Models\Rt::orderBy('nomor_rt')->get() as $rt)
                        <option value="{{ $rt->id }}" {{ old('rt_id', auth()->user()->rt_id) == $rt->id ? 'selected' : '' }}>
                            {{ $rt->nama_rt }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Nama Aset <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" class="form-select" required>
                        @foreach (['tenda' => 'Tenda', 'kursi' => 'Kursi', 'meja' => 'Meja', 'elektronik' => 'Elektronik', 'alat_kerja' => 'Alat Kerja', 'perlengkapan' => 'Perlengkapan', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('kategori') == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jumlah Total <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah_total" class="form-control" value="{{ old('jumlah_total', 1) }}" min="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Satuan <span class="text-danger">*</span></label>
                    <input type="text" name="satuan" class="form-control" value="{{ old('satuan', 'unit') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                    <select name="kondisi" class="form-select" required>
                        @foreach (['baik' => 'Baik', 'rusak_ringan' => 'Rusak Ringan', 'rusak_berat' => 'Rusak Berat', 'hilang' => 'Hilang'] as $v => $l)
                            <option value="{{ $v }}" {{ old('kondisi', 'baik') == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Lokasi Penyimpanan</label>
                    <input type="text" name="lokasi_penyimpanan" class="form-control" value="{{ old('lokasi_penyimpanan') }}"
                           placeholder="Contoh: Gudang RW / Rumah Ketua RW">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Foto Aset</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Perolehan</label>
                    <input type="date" name="tanggal_perolehan" class="form-control" value="{{ old('tanggal_perolehan') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Harga Perolehan</label>
                    <input type="number" name="harga_perolehan" class="form-control" value="{{ old('harga_perolehan') }}" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sumber Perolehan</label>
                    <input type="text" name="sumber_perolehan" class="form-control" value="{{ old('sumber_perolehan') }}"
                           placeholder="Beli / Sumbangan / Bantuan">
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('inventaris.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan</button>
        </div>
    </div>
</form>

@endsection