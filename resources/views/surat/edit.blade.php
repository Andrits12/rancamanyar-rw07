@extends('layouts.admin')

@section('title', 'Edit Pengajuan Surat: ' . $surat->kode_surat)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Edit Pengajuan Surat</h5>
        <small class="text-muted">Kode: {{ $surat->kode_surat }}</small>
    </div>
    <a href="{{ route('surat.show', $surat) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('surat.update', $surat) }}">
    @csrf
    @method('PUT')

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Jenis Surat</label>
                <input type="text" class="form-control" value="{{ $surat->jenisSurat->nama }}" readonly disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Keperluan <span class="text-danger">*</span></label>
                <textarea name="keperluan" class="form-control" rows="3" required>{{ old('keperluan', $surat->keperluan) }}</textarea>
            </div>
            <div class="mb-0">
                <label class="form-label">Catatan Tambahan</label>
                <textarea name="catatan_pemohon" class="form-control" rows="2">{{ old('catatan_pemohon', $surat->catatan_pemohon) }}</textarea>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('surat.show', $surat) }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Update
            </button>
        </div>
    </div>
</form>

@endsection