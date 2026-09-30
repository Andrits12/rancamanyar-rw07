@extends('layouts.admin')

@section('title', 'Detail Panic Button')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>#map-detail { height: 250px; border-radius: 8px; }</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">🚨 Detail Sinyal Darurat</h5>
        <small class="text-muted">{{ $panic->created_at->format('d F Y, H:i') }}</small>
    </div>
    <a href="{{ route('panic.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row g-3">
    <div class="col-md-7">
        <div class="card border-danger border-2 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h4>{{ $panic->jenis_label }}</h4>
                    <span class="badge {{ $panic->status_badge }}">{{ ucfirst(str_replace('_', ' ', $panic->status)) }}</span>
                </div>

                @if ($panic->keterangan)
                <p>{{ $panic->keterangan }}</p>
                @endif

                <table class="table table-sm">
                    <tr><th width="180">Pelapor</th><td>{{ $panic->user->name ?? '-' }}</td></tr>
                    <tr><th>No HP</th><td>{{ $panic->user->no_hp ?? '-' }}</td></tr>
                    <tr><th>RT</th><td>{{ $panic->rt->nama_rt ?? '-' }}</td></tr>
                    <tr><th>Lokasi</th><td>{{ $panic->alamat_lokasi ?? '-' }}</td></tr>
                    <tr><th>Waktu</th><td>{{ $panic->created_at->format('d F Y, H:i:s') }}</td></tr>
                    @if ($panic->penangan)
                    <tr><th>Ditangani Oleh</th><td>{{ $panic->penangan->name }}</td></tr>
                    <tr><th>Ditangani At</th><td>{{ $panic->ditangani_at?->format('d M Y, H:i') ?? '-' }}</td></tr>
                    @endif
                </table>

                @if ($panic->foto)
                    <img src="{{ asset('storage/' . $panic->foto) }}" class="img-fluid rounded mt-2" style="max-height: 300px;">
                @endif

                @if ($panic->latitude && $panic->longitude)
                    <div id="map-detail" class="mt-3"></div>
                @endif

                @if ($panic->catatan_penanganan)
                    <div class="alert alert-info mt-3">
                        <strong>Catatan Penanganan:</strong><br>
                        {{ $panic->catatan_penanganan }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-5">
        @can('panic.handle')
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-danger text-white"><strong><i class="bi bi-tools"></i> Tindak Lanjut</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('panic.update', $panic) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            @foreach (['baru' => 'Baru', 'ditangani' => 'Ditangani', 'selesai' => 'Selesai', 'false_alarm' => 'False Alarm'] as $v => $l)
                                <option value="{{ $v }}" {{ $panic->status == $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan Penanganan</label>
                        <textarea name="catatan_penanganan" class="form-control" rows="4">{{ $panic->catatan_penanganan }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle"></i> Update
                    </button>
                </form>
            </div>
        </div>
        @endcan

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white"><strong>Kontak Cepat</strong></div>
            <div class="card-body">
                <a href="tel:{{ $panic->user->no_hp }}" class="btn btn-success w-100 mb-2">
                    <i class="bi bi-telephone"></i> Telepon Pelapor
                </a>
                <a href="https://wa.me/{{ preg_replace('/^0/', '62', $panic->user->no_hp) }}" target="_blank" class="btn btn-success w-100">
                    <i class="bi bi-whatsapp"></i> WhatsApp Pelapor
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@if ($panic->latitude && $panic->longitude)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const lat = {{ $panic->latitude }}, lng = {{ $panic->longitude }};
    const m = L.map('map-detail').setView([lat, lng], 17);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(m);
    L.marker([lat, lng]).addTo(m).bindPopup('Lokasi Darurat').openPopup();
</script>
@endif
@endpush

@endsection