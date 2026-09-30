@extends('layouts.admin')

@section('title', 'Peta Digital')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #map { height: calc(100vh - 180px); border-radius: 8px; }
    .lokasi-list { max-height: calc(100vh - 180px); overflow-y: auto; }
    .lokasi-item {
        cursor: pointer;
        border-left: 4px solid #667eea;
        transition: all 0.2s;
        padding: 10px 12px;
        border-bottom: 1px solid #f1f5f9;
    }
    .lokasi-item:hover {
        background: #f8fafc;
    }
    .lokasi-item.active {
        background: #eef2ff;
    }
    .kategori-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
        color: #fff;
        font-weight: 600;
    }
    .leaflet-popup-content img {
        max-width: 200px;
        border-radius: 5px;
        margin-top: 5px;
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">🗺️ Peta Digital RW 07</h5>
        <small class="text-muted" id="total-lokasi">Total: {{ $lokasis->count() }} lokasi terpetakan</small>
    </div>
    @can('peta.create')
    <a href="{{ route('peta.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Lokasi
    </a>
    @endcan
</div>

{{-- Filter & Statistik --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-5">
                <input type="text" id="search-input" class="form-control form-control-sm"
                placeholder="Cari nama lokasi..." oninput="filterLokasi()">
            </div>
            <div class="col-md-4">
                <select id="filter-kategori" class="form-select form-select-sm" onchange="filterLokasi()">
                    <option value="">Semua Kategori</option>
                    @foreach (['rumah_warga' => '🏠 Rumah Warga', 'pos_ronda' => '🛡️ Pos Ronda', 'posyandu' => '👶 Posyandu', 'masjid' => '🕌 Masjid', 'sekolah' => '🏫 Sekolah', 'umkm' => '🏪 UMKM', 'aset_rw' => '📦 Aset RW', 'fasilitas_umum' => '🏛️ Fasilitas Umum', 'lainnya' => '📍 Lainnya'] as $v => $l)
                    <option value="{{ $v }}">{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" onclick="resetFilter()" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- PETA --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-2">
                <div id="map"></div>
            </div>
        </div>
    </div>

    {{-- DAFTAR LOKASI --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <strong><i class="bi bi-list-ul"></i> Daftar Lokasi</strong>
            </div>
            <div class="lokasi-list" id="lokasi-list">
                @forelse ($lokasis as $l)
                <div class="lokasi-item" data-id="{{ $l->id }}" data-lat="{{ $l->latitude }}" data-lng="{{ $l->longitude }}" onclick="focusMarker({{ $l->id }})">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <strong class="small">{{ $l->nama }}</strong>
                        <span class="kategori-badge" style="background: {{ $l->kategori_color }};">
                            {{ $l->kategori_label }}
                        </span>
                    </div>
                    @if ($l->alamat)
                        <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ Str::limit($l->alamat, 50) }}</small>
                    @endif
                    @if ($l->rt)
                        <br><small class="text-muted">{{ $l->rt->nama_rt }}</small>
                    @endif
                </div>
                @empty
                <div class="p-3 text-center text-muted">
                    <i class="bi bi-inbox" style="font-size: 30px;"></i>
                    <p class="mb-0 small mt-2">Belum ada lokasi terpetakan.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Data lokasi dari server
    const semuaLokasi = @json($lokasis);

    // Setup peta
    const defaultLat = -6.3289;
    const defaultLng = 107.3079;
    const map = L.map('map').setView([defaultLat, defaultLng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19,
    }).addTo(map);

    // Custom marker warna
    function buatMarkerIcon(warna) {
        return L.divIcon({
            className: 'custom-marker',
            html: `<div style="background: ${warna}; width: 30px; height: 30px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 3px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">
                       <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; transform: rotate(45deg); color: #fff; font-size: 14px;">📍</div>
                   </div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 30],
            popupAnchor: [0, -30],
        });
    }

    // Simpan marker yang aktif
    const activeMarkers = {};

    // Render marker + sidebar
    function renderLokasi(lokasiList) {
        // Hapus marker lama
        Object.values(activeMarkers).forEach(m => map.removeLayer(m));
        Object.keys(activeMarkers).forEach(k => delete activeMarkers[k]);

        // Hapus daftar lama
        const list = document.getElementById('lokasi-list');
        list.innerHTML = '';

        if (lokasiList.length === 0) {
            list.innerHTML = '<div class="p-3 text-center text-muted"><i class="bi bi-inbox" style="font-size: 30px;"></i><p class="mb-0 small mt-2">Tidak ada lokasi di kategori ini.</p></div>';
            return;
        }

        const bounds = [];

        lokasiList.forEach(l => {
            // Buat marker
            const marker = L.marker([l.latitude, l.longitude], { icon: buatMarkerIcon(l.kategori_color) })
                .addTo(map)
                .bindPopup(`
                    <div style="min-width: 220px;">
                        <h6 style="margin: 0 0 5px; font-weight: 700;">${l.nama}</h6>
                        <span style="background: ${l.kategori_color}; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 11px;">
                            ${l.kategori_label}
                        </span>
                        ${l.alamat ? `<p style="margin: 8px 0 5px; font-size: 12px;">📍 ${l.alamat}</p>` : ''}
                        <div style="display: flex; gap: 5px; margin-top: 10px;">
                            <a href="/peta/${l.id}" style="flex: 1; text-align: center; padding: 5px 10px; background: #667eea; color: #fff; border-radius: 5px; text-decoration: none; font-size: 12px;">Detail</a>
                            <a href="/peta/${l.id}/edit" style="flex: 1; text-align: center; padding: 5px 10px; background: #f59e0b; color: #fff; border-radius: 5px; text-decoration: none; font-size: 12px;">Edit</a>
                        </div>
                    </div>
                `);

            activeMarkers[l.id] = marker;
            bounds.push([l.latitude, l.longitude]);

            // Buat item sidebar
            const item = document.createElement('div');
            item.className = 'lokasi-item';
            item.dataset.id = l.id;
            item.innerHTML = `
                <div onclick="focusMarker(${l.id})" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <strong class="small">${l.nama}</strong>
                        <span class="kategori-badge" style="background: ${l.kategori_color};">${l.kategori_label}</span>
                    </div>
                    ${l.alamat ? `<small class="text-muted"><i class="bi bi-geo-alt"></i> ${l.alamat.substring(0, 50)}</small>` : ''}
                </div>
                <div class="mt-2 d-flex gap-1">
                    <a href="/peta/${l.id}" class="btn btn-sm btn-outline-info" style="font-size: 11px;">
                        <i class="bi bi-eye"></i> Detail
                    </a>
                    <a href="/peta/${l.id}/edit" class="btn btn-sm btn-outline-warning" style="font-size: 11px;">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                </div>
            `;
            list.appendChild(item);
        });

        // Fit peta
        if (bounds.length > 0) {
            if (bounds.length === 1) {
                map.setView(bounds[0], 17);
            } else {
                map.fitBounds(bounds, { padding: [50, 50], maxZoom: 17 });
            }
        }
    }

    // Filter berdasarkan input user
    function filterLokasi() {
        const kategori = document.getElementById('filter-kategori').value;
        const search = document.getElementById('search-input').value.toLowerCase();

        let filtered = semuaLokasi;

        if (kategori) {
            filtered = filtered.filter(l => l.kategori === kategori);
        }

        if (search) {
            filtered = filtered.filter(l => l.nama.toLowerCase().includes(search));
        }

        renderLokasi(filtered);

        // Update jumlah total
        const totalEl = document.getElementById('total-lokasi');
        if (totalEl) {
            totalEl.textContent = 'Total: ' + filtered.length + ' lokasi terpetakan';
        }
    }

    // Reset filter
    function resetFilter() {
        document.getElementById('filter-kategori').value = '';
        document.getElementById('search-input').value = '';
        filterLokasi();
    }

    // Focus ke marker
    function focusMarker(id) {
        const marker = activeMarkers[id];
        if (marker) {
            map.setView(marker.getLatLng(), 18);
            marker.openPopup();

            document.querySelectorAll('.lokasi-item').forEach(el => el.classList.remove('active'));
            document.querySelector(`.lokasi-item[data-id="${id}"]`)?.classList.add('active');
        }
    }

    // Render awal
    renderLokasi(semuaLokasi);
</script>


@endsection