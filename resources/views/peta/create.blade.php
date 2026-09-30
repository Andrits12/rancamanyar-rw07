@extends('layouts.admin')

@section('title', 'Tambah Lokasi Peta')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #map { height: 400px; border-radius: 8px; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Tambah Lokasi</h5>
        <small class="text-muted">Klik peta untuk menentukan koordinat</small>
    </div>
    <a href="{{ route('peta.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('peta.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required
                           placeholder="Contoh: Pos Ronda RT 01">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" class="form-select" required>
                        @foreach (['rumah_warga' => '🏠 Rumah Warga', 'pos_ronda' => '🛡️ Pos Ronda', 'posyandu' => '👶 Posyandu', 'masjid' => '🕌 Masjid', 'sekolah' => '🏫 Sekolah', 'umkm' => '🏪 UMKM', 'aset_rw' => '📦 Aset RW', 'fasilitas_umum' => '🏛️ Fasilitas Umum', 'lainnya' => '📍 Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('kategori') == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">RT (opsional)</label>
                    <select name="rt_id" class="form-select">
                        <option value="">-- Tidak spesifik RT --</option>
                        @foreach ($rts as $rt)
                            <option value="{{ $rt->id }}" {{ old('rt_id') == $rt->id ? 'selected' : '' }}>{{ $rt->nama_rt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kontak</label>
                    <input type="text" name="kontak" class="form-control" value="{{ old('kontak') }}"
                           placeholder="No HP / Nama penanggung jawab">
                </div>

                <div class="col-12">
                    <label class="form-label">Alamat / Patokan</label>
                    <textarea name="alamat" class="form-control" rows="2">{{ old('alamat') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Foto (opsional)</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="is_public" value="1" class="form-check-input" id="is_public"
                               {{ old('is_public', true) ? 'checked' : '' }}>
                        <label for="is_public" class="form-check-label">Tampilkan di peta publik</label>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">
                        <i class="bi bi-geo-alt-fill text-danger"></i> Klik di Peta untuk Tentukan Koordinat <span class="text-danger">*</span>
                    </label>
                    <div id="map" class="mb-2"></div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small">Latitude</label>
                            <input type="text" name="latitude" id="latitude" class="form-control form-control-sm"
                                   value="{{ old('latitude') }}" readonly required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Longitude</label>
                            <input type="text" name="longitude" id="longitude" class="form-control form-control-sm"
                                   value="{{ old('longitude') }}" readonly required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="getCurrentLocation()">
                        <i class="bi bi-crosshair"></i> Gunakan Lokasi Saya Sekarang
                    </button>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('peta.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan Lokasi</button>
        </div>
    </div>
</form>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const defaultLat = -6.3289;
    const defaultLng = 107.3079;

    const map = L.map('map').setView([defaultLat, defaultLng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

    function updateInputs(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(7);
        document.getElementById('longitude').value = lng.toFixed(7);
    }

    // Set dari old value kalau ada
    const oldLat = document.getElementById('latitude').value;
    const oldLng = document.getElementById('longitude').value;
    if (oldLat && oldLng) {
        marker.setLatLng([parseFloat(oldLat), parseFloat(oldLng)]);
        map.setView([parseFloat(oldLat), parseFloat(oldLng)], 17);
    } else {
        updateInputs(defaultLat, defaultLng);
    }

    map.on('click', e => {
        marker.setLatLng(e.latlng);
        updateInputs(e.latlng.lat, e.latlng.lng);
    });

    marker.on('dragend', () => {
        const pos = marker.getLatLng();
        updateInputs(pos.lat, pos.lng);
    });

    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(pos => {
                const lat = pos.coords.latitude, lng = pos.coords.longitude;
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng], 18);
                updateInputs(lat, lng);
            }, () => alert('Tidak bisa akses lokasi. Pastikan GPS aktif.'));
        } else {
            alert('Browser tidak mendukung GPS.');
        }
    }
</script>
@endpush

@endsection