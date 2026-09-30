@extends('layouts.admin')

@section('title', 'Buat Pengaduan')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #map { height: 300px; border-radius: 8px; }
    .preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; margin-top: 10px; }
    .preview-item { position: relative; }
    .preview-item img { width: 100%; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; }
    .preview-item .remove-btn {
        position: absolute; top: 5px; right: 5px;
        background: rgba(220, 38, 38, 0.9); color: #fff;
        border: none; border-radius: 50%; width: 22px; height: 22px;
        cursor: pointer; font-size: 12px;
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Buat Pengaduan</h5>
        <small class="text-muted">Sampaikan keluhan / laporan Anda</small>
    </div>
    <a href="{{ route('pengaduan.index') }}" class="btn btn-outline-secondary btn-sm">
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

<form method="POST" action="{{ route('pengaduan.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">

            <div class="col-12">
                <label class="form-label">Tingkat Pengaduan <span class="text-danger">*</span></label>
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="border rounded p-3 {{ old('tingkat', 'rt') == 'rt' ? 'border-primary bg-light' : '' }}"
                        style="cursor: pointer;" onclick="selectTingkat('rt')">
                        <input type="radio" name="tingkat" value="rt" id="tingkat-rt"
                        {{ old('tingkat', 'rt') == 'rt' ? 'checked' : '' }}>
                        <label for="tingkat-rt" style="cursor: pointer;">
                            <strong><i class="bi bi-house-door"></i> Pengaduan RT</strong>
                            <div class="small text-muted">
                                Masalah lingkungan RT: parkir, keributan, penerangan, dll.
                                Ditangani Ketua RT.
                            </div>
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 {{ old('tingkat') == 'rw' ? 'border-primary bg-light' : '' }}"
                    style="cursor: pointer;" onclick="selectTingkat('rw')">
                    <input type="radio" name="tingkat" value="rw" id="tingkat-rw"
                    {{ old('tingkat') == 'rw' ? 'checked' : '' }}>
                    <label for="tingkat-rw" style="cursor: pointer;">
                        <strong><i class="bi bi-building"></i> Pengaduan RW</strong>
                        <div class="small text-muted">
                            Kegiatan/acara besar, fasilitas RW, masalah antar-RT.
                            Ditangani Ketua RW.
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>

                <div class="col-md-6">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach (['infrastruktur' => 'Infrastruktur (jalan, lampu, dll)', 'keamanan' => 'Keamanan', 'kebersihan' => 'Kebersihan', 'kesehatan' => 'Kesehatan', 'sosial' => 'Sosial', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('kategori') == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Prioritas <span class="text-danger">*</span></label>
                    <select name="prioritas" class="form-select" required>
                        <option value="rendah" {{ old('prioritas') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                        <option value="sedang" {{ old('prioritas', 'sedang') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                        <option value="tinggi" {{ old('prioritas') == 'tinggi' ? 'selected' : '' }}>Tinggi (Darurat)</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Judul Pengaduan <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control" value="{{ old('judul') }}"
                           maxlength="200" required
                           placeholder="Contoh: Lampu jalan depan Blok C mati">
                </div>

                <div class="col-12">
                    <label class="form-label">Deskripsi Lengkap <span class="text-danger">*</span></label>
                    <textarea name="deskripsi" class="form-control" rows="5" required
                              placeholder="Jelaskan detail masalahnya...">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Foto / Video Pendukung</label>
                    <input type="file" name="lampiran[]" id="lampiran" class="form-control"
                           accept="image/*,video/*,.pdf" multiple>
                    <small class="text-muted">Bisa pilih lebih dari 1 file. Max 5MB per file.</small>
                    <div id="preview-grid" class="preview-grid"></div>
                </div>

                <div class="col-12">
                    <label class="form-label">
                        <i class="bi bi-geo-alt"></i> Lokasi Kejadian (Klik di peta)
                    </label>
                    <div id="map" class="mb-2"></div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text" name="latitude" id="latitude" class="form-control form-control-sm"
                                   placeholder="Latitude" value="{{ old('latitude') }}">
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="longitude" id="longitude" class="form-control form-control-sm"
                                   placeholder="Longitude" value="{{ old('longitude') }}">
                        </div>
                        <div class="col-12">
                            <input type="text" name="alamat_lokasi" class="form-control form-control-sm"
                                   placeholder="Alamat / patokan lokasi" value="{{ old('alamat_lokasi') }}">
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="getCurrentLocation()">
                                <i class="bi bi-crosshair"></i> Gunakan Lokasi Saya Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('pengaduan.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send"></i> Kirim Pengaduan
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Default center: Rancamanyar, Wancimekar
    const defaultLat = -6.3289;
    const defaultLng = 107.3079;

    const map = L.map('map').setView([defaultLat, defaultLng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

    // Isi form dari marker
    function updateMarkerInputs(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(7);
        document.getElementById('longitude').value = lng.toFixed(7);
    }

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateMarkerInputs(e.latlng.lat, e.latlng.lng);
    });

    marker.on('dragend', function(e) {
        const pos = marker.getLatLng();
        updateMarkerInputs(pos.lat, pos.lng);
    });

    // Load dari old value kalau ada
    const oldLat = document.getElementById('latitude').value;
    const oldLng = document.getElementById('longitude').value;
    if (oldLat && oldLng) {
        marker.setLatLng([parseFloat(oldLat), parseFloat(oldLng)]);
        map.setView([parseFloat(oldLat), parseFloat(oldLng)], 17);
    }

    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng], 17);
                updateMarkerInputs(lat, lng);
            }, function() {
                alert('Tidak bisa mengakses lokasi. Pastikan GPS aktif & izin lokasi diberikan.');
            });
        } else {
            alert('Browser tidak mendukung GPS.');
        }
    }

    // Preview upload
    document.getElementById('lampiran')?.addEventListener('change', function(e) {
        const grid = document.getElementById('preview-grid');
        grid.innerHTML = '';
        Array.from(e.target.files).forEach((file, i) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const div = document.createElement('div');
                    div.className = 'preview-item';
                    div.innerHTML = `<img src="${ev.target.result}">`;
                    grid.appendChild(div);
                };
                reader.readAsDataURL(file);
            } else {
                const div = document.createElement('div');
                div.className = 'preview-item d-flex align-items-center justify-content-center bg-light';
                div.style.height = '100px';
                div.style.borderRadius = '8px';
                div.innerHTML = '<i class="bi bi-file-earmark" style="font-size: 32px;"></i>';
                grid.appendChild(div);
            }
        });
    });
</script>
@endpush

@endsection