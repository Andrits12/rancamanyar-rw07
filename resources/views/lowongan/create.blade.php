@extends('layouts.admin')

@section('title', 'Buat Lowongan Kerja')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Buat Lowongan Kerja</h5>
    <a href="{{ route('lowongan.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('lowongan.store') }}">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Posisi / Judul <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control" value="{{ old('judul') }}" required
                           placeholder="Contoh: Staff Admin, Operator Produksi, Sales">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Perusahaan <span class="text-danger">*</span></label>
                    <input type="text" name="perusahaan" class="form-control" value="{{ old('perusahaan') }}" required
                           placeholder="PT ABC">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis <span class="text-danger">*</span></label>
                    <select name="jenis" class="form-select" required>
                        @foreach (['full_time' => 'Full Time', 'part_time' => 'Part Time', 'kontrak' => 'Kontrak', 'magang' => 'Magang', 'freelance' => 'Freelance'] as $v => $l)
                            <option value="{{ $v }}" {{ old('jenis') == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Lokasi Kerja</label>
                    <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi') }}"
                           placeholder="Karawang / Jakarta / Remote">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Gaji Min</label>
                    <input type="text" name="gaji_min" class="form-control" value="{{ old('gaji_min') }}"
                           placeholder="3.000.000">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Gaji Max</label>
                    <input type="text" name="gaji_max" class="form-control" value="{{ old('gaji_max') }}"
                           placeholder="5.000.000">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Buka <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_buka" class="form-control" value="{{ old('tanggal_buka', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deadline</label>
                    <input type="date" name="deadline" class="form-control" value="{{ old('deadline') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi Pekerjaan <span class="text-danger">*</span></label>
                    <textarea name="deskripsi" class="form-control" rows="4" required>{{ old('deskripsi') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kualifikasi</label>
                    <textarea name="kualifikasi" class="form-control" rows="4"
                              placeholder="1. Pendidikan minimal SMA&#10;2. Usia max 35 tahun&#10;3. ...">{{ old('kualifikasi') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggung Jawab</label>
                    <textarea name="tanggung_jawab" class="form-control" rows="4">{{ old('tanggung_jawab') }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kontak Nama (HRD)</label>
                    <input type="text" name="kontak_nama" class="form-control" value="{{ old('kontak_nama') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kontak HP</label>
                    <input type="text" name="kontak_hp" class="form-control" value="{{ old('kontak_hp') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kontak Email</label>
                    <input type="email" name="kontak_email" class="form-control" value="{{ old('kontak_email') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="ditutup" {{ old('status') == 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="is_pinned" value="1" class="form-check-input" id="is_pinned"
                               {{ old('is_pinned') ? 'checked' : '' }}>
                        <label for="is_pinned" class="form-check-label">
                            <i class="bi bi-pin-angle-fill text-warning"></i> Sematkan di atas
                        </label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan Tambahan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('lowongan.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan Lowongan</button>
        </div>
    </div>
</form>

@endsection