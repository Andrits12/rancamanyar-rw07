@extends('layouts.admin')

@section('title', $lokasi->nama)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #map-detail { height: 350px; border-radius: 8px; }
</style>
@endpush

@section('content')


<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">{{ $lokasi->nama }}</h5>
        <small class="text-muted">
            <span class="badge" style="background: {{ $lokasi->kategori_color }};">{{ $lokasi->kategori_label }}</span>
        </small>
    </div>
    <div>
    @can('peta.edit')
    <a href="/peta/{{ $lokasi->id }}/edit" class="btn btn-warning btn-sm">
        <i class="bi bi-pencil"></i> Edit
    </a>
    @endcan
        <a href="{{ route('peta.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali ke Peta
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-5">
        @if ($lokasi->foto_url)
        <div class="card border-0 shadow-sm mb-3">
            <img src="{{ $lokasi->foto_url }}" class="card-img-top" style="max-height: 300px; object-fit: cover;">
        </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Informasi</strong></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th width="140">Nama</th><td>{{ $lokasi->nama }}</td></tr>
                    <tr><th>Kategori</th><td>{{ $lokasi->kategori_label }}</td></tr>
                    @if ($lokasi->rt)
                        <tr><th>RT</th><td>{{ $lokasi->rt->nama_rt }}</td></tr>
                    @endif
                    @if ($lokasi->alamat)
                        <tr><th>Alamat</th><td>{{ $lokasi->alamat }}</td></tr>
                    @endif
                    @if ($lokasi->kontak)
                        <tr><th>Kontak</th><td>{{ $lokasi->kontak }}</td></tr>
                    @endif
                    <tr><th>Koordinat</th><td><code>{{ $lokasi->latitude }}, {{ $lokasi->longitude }}</code></td></tr>
                    <tr><th>Status</th><td>
                        @if ($lokasi->is_active) <span class="badge bg-success">Aktif</span>
                        @else <span class="badge bg-secondary">Nonaktif</span> @endif
                    </td></tr>
                    @if ($lokasi->pembuat)
                        <tr><th>Ditambahkan</th><td><small>{{ $lokasi->pembuat->name }} — {{ $lokasi->created_at->format('d M Y') }}</small></td></tr>
                    @endif
                </table>

                @if ($lokasi->deskripsi)
                    <hr>
                    <p class="mb-0">{{ $lokasi->deskripsi }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-2">
                <div id="map-detail"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const lat = {{ $lokasi->latitude }};
    const lng = {{ $lokasi->longitude }};
    const nama = "{{ $lokasi->nama }}";
    const warna = "{{ $lokasi->kategori_color }}";

    const map = L.map('map-detail').setView([lat, lng], 18);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    const icon = L.divIcon({
        className: 'custom-marker',
        html: `<div style="background: ${warna}; width: 30px; height: 30px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 3px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"></div>`,
        iconSize: [30, 30],
        iconAnchor: [15, 30],
    });

    L.marker([lat, lng], { icon: icon }).addTo(map).bindPopup(nama).openPopup();
</script>
@endpush

@endsection