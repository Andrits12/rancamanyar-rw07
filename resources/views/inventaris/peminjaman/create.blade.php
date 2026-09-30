@extends('layouts.admin')

@section('title', 'Catat Peminjaman Aset')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Catat Peminjaman Aset</h5>
        <small class="text-muted">Input peminjaman atas nama warga</small>
    </div>
    <a href="{{ route('peminjaman-aset.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    Form ini untuk <strong>Ketua RT/RW</strong>. Warga yang ingin meminjam aset,
    silakan lapor langsung ke Anda, lalu input peminjamannya di sini.
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('peminjaman-aset.store') }}">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">

                {{-- Pilih Peminjam --}}
                <div class="col-md-6">
                    <label class="form-label">Peminjam (Warga) <span class="text-danger">*</span></label>
                    <select name="peminjam_id" class="form-select" required>
                        <option value="">-- Pilih Warga yang Meminjam --</option>
                        @foreach ($wargas as $w)
                            <option value="{{ $w->id }}" {{ old('peminjam_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->name }} — {{ $w->rt->nama_rt ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">
                        Pilih nama warga yang sudah lapor ke Anda
                    </small>
                </div>

                {{-- Pilih Aset --}}
                <div class="col-md-6">
                    <label class="form-label">Aset yang Dipinjam <span class="text-danger">*</span></label>
                    <select name="aset_id" id="aset_id" class="form-select" required onchange="updateStok()">
                        <option value="">-- Pilih Aset --</option>
                        @foreach ($asets as $a)
                            <option value="{{ $a->id }}"
                                    data-stok="{{ $a->jumlah_tersedia }}"
                                    data-satuan="{{ $a->satuan }}"
                                    {{ old('aset_id', $selectedAset->id ?? '') == $a->id ? 'selected' : '' }}>
                                [{{ $a->pemilik === 'rw' ? 'RW' : 'RT ' . ($a->rt->nomor_rt ?? '?') }}]
                                {{ $a->nama }} — Tersedia: {{ $a->jumlah_tersedia }} {{ $a->satuan }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted" id="stok-info"></small>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah" id="jumlah" class="form-control" value="{{ old('jumlah', 1) }}" min="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_pinjam" class="form-control" value="{{ old('tanggal_pinjam', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Rencana Kembali <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_rencana_kembali" class="form-control" value="{{ old('tanggal_rencana_kembali', now()->addDays(3)->format('Y-m-d')) }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Keperluan <span class="text-danger">*</span></label>
                    <textarea name="keperluan" class="form-control" rows="3" required
                              placeholder="Contoh: Untuk acara pernikahan keluarga">{{ old('keperluan') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Catatan Tambahan</label>
                    <textarea name="catatan_peminjam" class="form-control" rows="2">{{ old('catatan_peminjam') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('peminjaman-aset.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Catat Peminjaman
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
function updateStok() {
    const select = document.getElementById('aset_id');
    const option = select.options[select.selectedIndex];
    const info = document.getElementById('stok-info');
    const jumlah = document.getElementById('jumlah');

    if (option.value) {
        const stok = option.dataset.stok;
        const satuan = option.dataset.satuan;
        info.textContent = `Stok tersedia: ${stok} ${satuan}`;
        jumlah.max = stok;
    } else {
        info.textContent = '';
        jumlah.removeAttribute('max');
    }
}

document.addEventListener('DOMContentLoaded', updateStok);
</script>
@endpush

@endsection