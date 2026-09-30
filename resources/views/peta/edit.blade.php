@extends('layouts.admin')

@section('title', 'Edit Lokasi: ' . $lokasi->nama)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #map { height: 400px; border-radius: 8px; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Edit Lokasi: {{ $lokasi->nama }}</h5>
    <a href="{{ route('peta.show', $lokasi) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('peta.update', $lokasi) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $lokasi->nama) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" class="form-select" required>
                        @foreach (['rumah_warga' => '🏠 Rumah Warga', 'pos_ronda' => '🛡️ Pos Ronda', 'posyandu' => '👶 Posyandu', 'masjid' => '🕌 Masjid', 'sekolah' => '🏫 Sekolah', 'umkm' => '🏪 UMKM', 'aset_rw' => '📦 Aset RW', 'fasilitas_umum' => '🏛️ Fasilitas Umum', 'lainnya' => '📍 Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('kategori', $lokasi->kategori) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">RT</label>
                    <select name="rt_id" class="form-select">
                        <option value="">-- Tidak spesifik RT --</option>
                        @foreach ($rts as $rt)
                            <option value="{{ $rt->id }}" {{ old('rt_id', $lokasi->rt_id) == $rt->id ? 'selected' : '' }}>{{ $rt->nama_rt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kontak</label>
                    <input type="text" name="kontak" class="form-control" value="{{ old('kontak', $lokasi->kontak) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $lokasi->alamat) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $lokasi->deskripsi) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Foto</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                    @if ($lokasi->foto_url)
                        <img src="{{ $lokasi->foto_url }}" style="max-width: 150px; border-radius: 5px;" class="mt-2">
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input type="checkbox" name="is_public" value="1" class="form-check-input"
                               id="is_public" {{ old('is_public', $lokasi->is_public) ? 'checked' : '' }}>
                        <label for="is_public" class="form-check-label">Tampilkan di peta publik</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input"
                               id="is_active" {{ old('is_active', $lokasi->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">Aktif</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Klik di Peta untuk Update Koordinat</label>
                    <div id="map" class="mb-2"></div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small">Latitude</label>
                            <input type="text" name="latitude" id="latitude" class="form-control form-control-sm"
                                   value="{{ old('latitude', $lokasi->latitude) }}" readonly required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Longitude</label>
                            <input type="text" name="longitude" id="longitude" class="form-control form-control-sm"
                                   value="{{ old('longitude', $lokasi->longitude) }}" readonly required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('peta.show', $lokasi) }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Update</button>
        </div>
    </div>
</form>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const lat = {{ $lokasi->latitude }};
    const lng = {{ $lokasi->longitude }};

    const map = L.map('map').setView([lat, lng], 17);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    let marker = L.marker([lat, lng], { draggable: true }).addTo(map);

    function updateInputs(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(7);
        document.getElementById('longitude').value = lng.toFixed(7);
    }

    map.on('click', e => {
        marker.setLatLng(e.latlng);
        updateInputs(e.latlng.lat, e.latlng.lng);
    });

    marker.on('dragend', () => {
        const pos = marker.getLatLng();
        updateInputs(pos.lat, pos.lng);
    });

    // Auto-fit semua marker ke layar
    if (lokasis.length > 0) {
        const group = L.featureGroup(Object.values(markers));
        map.fitBounds(group.getBounds().pad(0.1));
    }
</script>
@endpush

@endsection