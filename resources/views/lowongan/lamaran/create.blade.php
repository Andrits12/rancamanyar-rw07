@extends('layouts.admin')

@section('title', 'Lamar: ' . $lowongan->judul)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Lamar Lowongan</h5>
        <small class="text-muted">{{ $lowongan->judul }} — {{ $lowongan->perusahaan }}</small>
    </div>
    <a href="{{ route('lowongan.show', $lowongan) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    Pastikan data Anda benar. RT Anda akan memverifikasi lamaran ini sebelum diteruskan ke perusahaan.
</div>

<form method="POST" action="{{ route('lamaran.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="lowongan_id" value="{{ $lowongan->id }}">

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Pelamar</label>
                    <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label">NIK</label>
                    <input type="text" class="form-control" value="{{ auth()->user()->nik }}" readonly disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label">RT</label>
                    <input type="text" class="form-control" value="{{ auth()->user()->rt->nama_rt ?? '-' }}" readonly disabled>
                </div>

                <div class="col-md-6">
                    <label class="form-label">No HP Aktif <span class="text-danger">*</span></label>
                    <input type="text" name="no_hp_pelamar" class="form-control" value="{{ old('no_hp_pelamar', auth()->user()->no_hp) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email_pelamar" class="form-control" value="{{ old('email_pelamar', auth()->user()->email) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Upload CV <span class="text-danger">*</span></label>
                    <input type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx" required>
                    <small class="text-muted">PDF/DOC/DOCX max 5MB</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Surat Lamaran</label>
                    <input type="file" name="surat_lamaran" class="form-control" accept=".pdf,.doc,.docx">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Portfolio (opsional)</label>
                    <input type="file" name="portfolio" class="form-control" accept=".pdf,.doc,.docx,.zip">
                </div>

                <div class="col-12">
                    <label class="form-label">Pengalaman Kerja</label>
                    <textarea name="pengalaman" class="form-control" rows="3"
                              placeholder="Contoh: 2 tahun sebagai Staff Admin di PT XYZ">{{ old('pengalaman') }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Motivasi Melamar <span class="text-danger">*</span></label>
                    <textarea name="motivasi" class="form-control" rows="4" required
                              placeholder="Jelaskan mengapa Anda cocok untuk posisi ini...">{{ old('motivasi') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('lowongan.show', $lowongan) }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Kirim Lamaran</button>
        </div>
    </div>
</form>

@endsection