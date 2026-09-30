@extends('layouts.admin')

@section('title', 'Edit: ' . $pengumuman->judul)

@push('styles')
<style>
    #preview-img { max-width: 100%; border-radius: 8px; margin-top: 10px; display: none; }
</style>
@endpush

@section('content')

@php $isBerita = $pengumuman->tipe === 'berita'; @endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Edit {{ $isBerita ? 'Berita' : 'Pengumuman' }}</h5>
        <small class="text-muted">{{ $pengumuman->judul }}</small>
    </div>
    <a href="{{ route('pengumuman.index', ['tipe' => $pengumuman->tipe]) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <strong>Ada kesalahan:</strong>
    <ul class="mb-0 mt-2">
        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('pengumuman.update', $pengumuman) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Judul <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control"
                           value="{{ old('judul', $pengumuman->judul) }}" required maxlength="200">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" class="form-select" required>
                        @foreach (['umum' => 'Umum', 'keamanan' => 'Keamanan', 'kegiatan' => 'Kegiatan', 'kesehatan' => 'Kesehatan', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('kategori', $pengumuman->kategori) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Ringkasan</label>
                    <textarea name="ringkasan" class="form-control" rows="2" maxlength="500">{{ old('ringkasan', $pengumuman->ringkasan) }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Konten <span class="text-danger">*</span></label>
                    <textarea name="konten" class="form-control" rows="10" required>{{ old('konten', $pengumuman->konten) }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Gambar Utama</label>
                    <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
                    <small class="text-muted">Max 2MB. Kosongkan kalau tidak ganti.</small>
                    @if ($pengumuman->gambar)
                        <img src="{{ $pengumuman->gambar_url }}" class="mt-2" style="max-width: 200px; border-radius: 8px;">
                    @endif
                    <img id="preview-img" alt="Preview">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="draft" {{ old('status', $pengumuman->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $pengumuman->status) == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="arsip" {{ old('status', $pengumuman->status) == 'arsip' ? 'selected' : '' }}>Arsip</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tanggal Publish</label>
                    <input type="datetime-local" name="published_at" class="form-control"
                           value="{{ old('published_at', $pengumuman->published_at?->format('Y-m-d\TH:i')) }}">
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_pinned" id="is_pinned" value="1"
                               class="form-check-input" {{ old('is_pinned', $pengumuman->is_pinned) ? 'checked' : '' }}>
                        <label for="is_pinned" class="form-check-label">
                            <i class="bi bi-pin-angle-fill text-warning"></i> Sematkan di atas
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('pengumuman.index', ['tipe' => $pengumuman->tipe]) }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Update
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    document.getElementById('gambar')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                const img = document.getElementById('preview-img');
                img.src = ev.target.result;
                img.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush

@endsection