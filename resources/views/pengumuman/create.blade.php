@extends('layouts.admin')

@section('title', 'Buat ' . ($tipe === 'berita' ? 'Berita' : 'Pengumuman'))

@push('styles')
<style>
    #preview-img { max-width: 100%; border-radius: 8px; margin-top: 10px; display: none; }
</style>
@endpush

@section('content')

@php $isBerita = $tipe === 'berita'; @endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Buat {{ $isBerita ? 'Berita' : 'Pengumuman' }}</h5>
        <small class="text-muted">Isi form di bawah ini</small>
    </div>
    <a href="{{ route('pengumuman.index', ['tipe' => $tipe]) }}" class="btn btn-outline-secondary btn-sm">
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

<form method="POST" action="{{ route('pengumuman.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="tipe" value="{{ $tipe }}">

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Judul <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror"
                           value="{{ old('judul') }}" required maxlength="200">
                    @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" class="form-select" required>
                        @foreach (['umum' => 'Umum', 'keamanan' => 'Keamanan', 'kegiatan' => 'Kegiatan', 'kesehatan' => 'Kesehatan', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('kategori') == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Ringkasan</label>
                    <textarea name="ringkasan" class="form-control" rows="2" maxlength="500"
                              placeholder="Ringkasan singkat (opsional, akan tampil di daftar)">{{ old('ringkasan') }}</textarea>
                    <small class="text-muted">Maksimal 500 karakter</small>
                </div>

                <div class="col-12">
                    <label class="form-label">Konten <span class="text-danger">*</span></label>
                    <textarea name="konten" class="form-control @error('konten') is-invalid @enderror"
                              rows="10" required>{{ old('konten') }}</textarea>
                    @error('konten') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted">Isi lengkap pengumuman / berita</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Gambar Utama</label>
                    <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
                    <small class="text-muted">Max 2MB (JPG, PNG, WEBP)</small>
                    <img id="preview-img" alt="Preview">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="arsip" {{ old('status') == 'arsip' ? 'selected' : '' }}>Arsip</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tanggal Publish</label>
                    <input type="datetime-local" name="published_at" class="form-control"
                           value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                    <small class="text-muted">Kosongkan = sekarang</small>
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_pinned" id="is_pinned" value="1"
                               class="form-check-input" {{ old('is_pinned') ? 'checked' : '' }}>
                        <label for="is_pinned" class="form-check-label">
                            <i class="bi bi-pin-angle-fill text-warning"></i>
                            Sematkan di atas (Pinned)
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('pengumuman.index', ['tipe' => $tipe]) }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Simpan
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