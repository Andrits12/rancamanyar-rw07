@extends('layouts.admin')

@section('title', 'Kirim Sinyal Darurat')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #map { height: 250px; border-radius: 8px; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1 text-danger">🚨 Kirim Sinyal Darurat</h5>
        <small class="text-muted">Gunakan hanya saat darurat!</small>
    </div>
    <a href="{{ route('panic.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('panic.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card border-danger border-2 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Jenis Darurat <span class="text-danger">*</span></label>
                    <select name="jenis" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <option value="kebakaran">🔥 Kebakaran</option>
                        <option value="medis">🚑 Medis / Kesehatan</option>
                        <option value="kriminal">🚨 Kriminal / Kejahatan</option>
                        <option value="bencana">⚠️ Bencana Alam</option>
                        <option value="lainnya">❓ Lainnya</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Foto (opsional)</label>
                    <input type="file" name="foto" class="form-control" accept="image/*" capture="environment">
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3"
                              placeholder="Jelaskan situasi darurat..."></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Lokasi (Klik di peta)</label>
                    <div id="map" class="mb-2"></div>
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <input type="text" name="alamat_lokasi" class="form-control form-control-sm"
                           placeholder="Alamat / patokan lokasi">
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('panic.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-danger btn-lg"
                    onclick="return confirm('⚠️ Kirim sinyal darurat? Pastikan ini bukan tes.')">
                <i class="bi bi-exclamation-triangle"></i> KIRIM SINYAL DARURAT
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([-6.3289, 107.3079], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    const marker = L.marker([-6.3289, 107.3079], { draggable: true }).addTo(map);

    function update(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(7);
        document.getElementById('longitude').value = lng.toFixed(7);
    }

    map.on('click', e => { marker.setLatLng(e.latlng); update(e.latlng.lat, e.latlng.lng); });
    marker.on('dragend', () => { const p = marker.getLatLng(); update(p.lat, p.lng); });

    // Auto-detect GPS
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            const lat = pos.coords.latitude, lng = pos.coords.longitude;
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 17);
            update(lat, lng);
        });
    }
</script>
@endpush

@endsection