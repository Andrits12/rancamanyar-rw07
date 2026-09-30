@extends('layouts.admin')

@section('title', 'Ajukan Surat')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Ajukan Surat</h5>
        <small class="text-muted">Isi form pengajuan surat</small>
    </div>
    <a href="{{ route('surat.index') }}" class="btn btn-outline-secondary btn-sm">
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

<form method="POST" action="{{ route('surat.store') }}">
    @csrf

    <div class="row g-3">
        {{-- Pilih Jenis Surat --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><strong>1. Pilih Jenis Surat</strong></div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach ($jenisSurats as $js)
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100 jenis-card {{ old('jenis_surat_id') == $js->id ? 'border-primary bg-light' : '' }}"
                                 style="cursor: pointer;" onclick="selectJenis({{ $js->id }})">
                                <input type="radio" name="jenis_surat_id" value="{{ $js->id }}"
                                       id="js-{{ $js->id }}" class="d-none jenis-radio"
                                       {{ old('jenis_surat_id') == $js->id ? 'checked' : '' }} required>
                                <label for="js-{{ $js->id }}" style="cursor: pointer;" class="w-100">
                                    <strong><i class="bi bi-file-earmark-text"></i> {{ $js->nama }}</strong>
                                    @if ($js->deskripsi)
                                        <div class="small text-muted mt-1">{{ Str::limit($js->deskripsi, 80) }}</div>
                                    @endif
                                    @if ($js->syarat)
                                        <div class="small text-secondary mt-2 border-top pt-2">
                                            <strong>Syarat:</strong>
                                            <div style="white-space: pre-line; font-size: 11px;">{{ $js->syarat }}</div>
                                        </div>
                                    @endif
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @error('jenis_surat_id') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Keperluan --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><strong>2. Detail Permohonan</strong></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Keperluan <span class="text-danger">*</span></label>
                        <textarea name="keperluan" class="form-control" rows="3" required
                                  placeholder="Jelaskan keperluan surat ini...">{{ old('keperluan') }}</textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Catatan Tambahan (opsional)</label>
                        <textarea name="catatan_pemohon" class="form-control" rows="2">{{ old('catatan_pemohon') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Pemohon --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><strong>3. Data Pemohon (otomatis)</strong></div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <small class="text-muted">Nama</small>
                            <div class="fw-bold">{{ auth()->user()->name }}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">NIK</small>
                            <div class="fw-bold">{{ auth()->user()->nik }}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">RT</small>
                            <div class="fw-bold">{{ auth()->user()->rt->nama_rt ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-3">
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('surat.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send"></i> Ajukan Surat
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    function selectJenis(id) {
        document.getElementById('js-' + id).checked = true;
        document.querySelectorAll('.jenis-card').forEach(el => {
            el.classList.remove('border-primary', 'bg-light');
        });
        event.currentTarget.classList.add('border-primary', 'bg-light');
    }
</script>
@endpush

@endsection