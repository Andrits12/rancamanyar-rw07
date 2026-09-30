@extends('layouts.admin')

@section('title', 'Catat Kematian')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Catat Kematian</h5>
    <a href="{{ route('kematian.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i>
    <strong>Perhatian:</strong> Setelah disimpan, status warga akan otomatis berubah menjadi <strong>Meninggal</strong>.
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('kematian.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold mb-3 text-primary">🕊️ Data Almarhum/ah</h6>
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">Pilih Warga <span class="text-danger">*</span></label>
                    <select name="warga_id" class="form-select" required>
                        <option value="">-- Pilih Warga --</option>
                        @foreach ($wargas as $w)
                            <option value="{{ $w->id }}" {{ old('warga_id', $selectedWarga->id ?? '') == $w->id ? 'selected' : '' }}>
                                {{ $w->nik }} — {{ $w->nama }} ({{ $w->rt->nama_rt ?? '-' }}) — {{ $w->umur }} th
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hanya warga dengan status "Hidup" yang muncul</small>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Meninggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_meninggal" class="form-control" value="{{ old('tanggal_meninggal', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jam Meninggal</label>
                    <input type="time" name="jam_meninggal" class="form-control" value="{{ old('jam_meninggal') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tempat Meninggal</label>
                    <input type="text" name="tempat_meninggal" class="form-control" value="{{ old('tempat_meninggal') }}"
                           placeholder="RS / Rumah / Tempat lain">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sebab <span class="text-danger">*</span></label>
                    <select name="sebab" class="form-select" required>
                        @foreach (['sakit' => '🏥 Sakit', 'kecelakaan' => '🚗 Kecelakaan', 'usia_lanjut' => '👴 Usia Lanjut', 'wabah' => '⚠️ Wabah', 'lainnya' => '❓ Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('sebab', 'sakit') == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Keterangan Sebab</label>
                    <input type="text" name="keterangan_sebab" class="form-control" value="{{ old('keterangan_sebab') }}">
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-primary">⚰️ Pemakaman</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Tempat Pemakaman</label>
                    <input type="text" name="tempat_pemakaman" class="form-control" value="{{ old('tempat_pemakaman') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pemakaman</label>
                    <input type="date" name="tanggal_pemakaman" class="form-control" value="{{ old('tanggal_pemakaman') }}">
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-primary">📄 Akta</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">No Akta Kematian</label>
                    <input type="text" name="no_akta_kematian" class="form-control" value="{{ old('no_akta_kematian') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Akta</label>
                    <input type="date" name="tanggal_akta" class="form-control" value="{{ old('tanggal_akta') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Upload Dokumen</label>
                    <input type="file" name="dokumen_akta" class="form-control" accept=".pdf,image/*">
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('kematian.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin catat kematian? Status warga akan berubah.')">
                <i class="bi bi-check-circle"></i> Simpan
            </button>
        </div>
    </div>
</form>

@endsection

