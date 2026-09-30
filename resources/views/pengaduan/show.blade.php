@extends('layouts.admin')

@section('title', 'Detail Pengaduan: ' . $pengaduan->kode_tiket)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #map-detail { height: 250px; border-radius: 8px; }
    .timeline { position: relative; padding-left: 30px; }
    .timeline::before {
        content: ''; position: absolute; left: 8px; top: 0; bottom: 0;
        width: 2px; background: #e2e8f0;
    }
    .timeline-item { position: relative; margin-bottom: 20px; }
    .timeline-item::before {
        content: ''; position: absolute; left: -30px; top: 5px;
        width: 18px; height: 18px; border-radius: 50%;
        background: #667eea; border: 3px solid #fff; box-shadow: 0 0 0 2px #e2e8f0;
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Detail Pengaduan</h5>
        <small class="text-muted">Kode: <code>{{ $pengaduan->kode_tiket }}</code></small>
    </div>
    <div>
        @can('pengaduan.edit')
        @if (auth()->user()->hasRole('warga') === false || $pengaduan->status === 'baru')
        <a href="{{ route('pengaduan.edit', $pengaduan) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endif
        @endcan
        @can('pengaduan.delete')
        <form method="POST" action="{{ route('pengaduan.destroy', $pengaduan) }}" class="d-inline"
              onsubmit="return confirm('Yakin hapus pengaduan ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </form>
        @endcan
        <a href="{{ route('pengaduan.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    {{-- Kolom Kiri: Detail --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge bg-info text-dark">{{ ucfirst($pengaduan->kategori) }}</span>
                    <span class="badge {{ $pengaduan->prioritas_badge }}">Prioritas {{ ucfirst($pengaduan->prioritas) }}</span>
                    <span class="badge {{ $pengaduan->status_badge }}">{{ ucfirst($pengaduan->status) }}</span>
                </div>

                <h4>{{ $pengaduan->judul }}</h4>

                <div class="text-muted small mb-3 pb-3 border-bottom">
                    <i class="bi bi-person"></i> {{ $pengaduan->user->name ?? '-' }}
                    • {{ $pengaduan->rt->nama_rt ?? '-' }}
                    • <i class="bi bi-clock"></i> {{ $pengaduan->created_at->format('d M Y, H:i') }}
                </div>

                <p style="white-space: pre-line; line-height: 1.8;">{{ $pengaduan->deskripsi }}</p>

                @if ($pengaduan->alamat_lokasi)
                    <div class="alert alert-light border">
                        <i class="bi bi-geo-alt-fill text-danger"></i> {{ $pengaduan->alamat_lokasi }}
                    </div>
                @endif

                @if ($pengaduan->latitude && $pengaduan->longitude)
                    <div id="map-detail" class="mb-3"></div>
                @endif
            </div>
        </div>

        {{-- Lampiran --}}
        @if ($pengaduan->lampirans->count() > 0)
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white"><strong><i class="bi bi-paperclip"></i> Lampiran ({{ $pengaduan->lampirans->count() }})</strong></div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach ($pengaduan->lampirans as $lamp)
                        <div class="col-md-4">
                            @if ($lamp->tipe === 'foto')
                                <a href="{{ $lamp->file_url }}" target="_blank">
                                    <img src="{{ $lamp->file_url }}" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;">
                                </a>
                            @elseif ($lamp->tipe === 'video')
                                <video controls class="w-100 rounded" style="max-height: 150px;">
                                    <source src="{{ $lamp->file_url }}">
                                </video>
                            @else
                                <a href="{{ $lamp->file_url }}" target="_blank" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-file-earmark-pdf"></i> {{ $lamp->nama_asli ?? 'Dokumen' }}
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Timeline Tindak Lanjut --}}
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white"><strong><i class="bi bi-clock-history"></i> Riwayat Tindak Lanjut</strong></div>
            <div class="card-body">
                @php
                    $allEvents = collect();
                    // Event awal: pengaduan dibuat
                    $allEvents->push([
                        'waktu' => $pengaduan->created_at,
                        'user' => $pengaduan->user->name ?? '-',
                        'text' => 'Pengaduan dibuat',
                        'status' => 'baru',
                        'catatan' => null,
                    ]);
                    // Event tindak lanjut
                    foreach ($pengaduan->tindakLanjuts as $tl) {
                        $allEvents->push([
                            'waktu' => $tl->created_at,
                            'user' => $tl->user->name ?? '-',
                            'text' => 'Status: ' . ucfirst($tl->status_baru),
                            'status' => $tl->status_baru,
                            'catatan' => $tl->catatan,
                        ]);
                    }
                    $allEvents = $allEvents->sortByDesc('waktu');
                @endphp

                <div class="timeline">
                    @foreach ($allEvents as $ev)
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $ev['text'] }}</strong>
                                <small class="text-muted">{{ $ev['waktu']->format('d M Y, H:i') }}</small>
                            </div>
                            <small class="text-muted">oleh {{ $ev['user'] }}</small>
                            @if ($ev['catatan'])
                                <div class="mt-1 small">{{ $ev['catatan'] }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
                @can('pengaduan.handle')
                @if ($pengaduan->tingkat === 'rt' && $pengaduan->status_eskalasi === 'tidak' && auth()->user()->hasAnyRole(['ketua_rt', 'super_admin']))
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-warning text-dark">
                        <strong><i class="bi bi-arrow-up-circle"></i> Eskalasi ke RW</strong>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">
                            Gunakan eskalasi jika masalah ini butuh penanganan RW
                            (misal: butuh dana, fasilitas RW, atau masalah antar-RT).
                        </p>
                        <form method="POST" action="{{ route('pengaduan.eskalasi', $pengaduan) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Alasan Eskalasi <span class="text-danger">*</span></label>
                                <textarea name="alasan" class="form-control" rows="3" required
                                placeholder="Kenapa perlu ditangani RW?"></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning w-100"
                            onclick="return confirm('Yakin eskalasi ke RW?')">
                            <i class="bi bi-arrow-up-circle"></i> Eskalasi ke RW
                        </button>
                    </form>
                </div>
            </div>
            @endif
            
            @endcan
            @if ($pengaduan->status_eskalasi !== 'tidak')
            <div class="alert alert-warning mt-3">
                <i class="bi bi-info-circle"></i>
                Pengaduan ini <strong>{{ $pengaduan->status_eskalasi === 'dieskalasi' ? 'telah dieskalasi ke RW' : 'merupakan hasil eskalasi dari RT' }}</strong>.
                @if ($pengaduan->pengaduanAsal)
                <a href="{{ route('pengaduan.show', $pengaduan->pengaduanAsal) }}">Lihat pengaduan asal</a>
                @endif
            </div>
            @endif
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Aksi Tindak Lanjut --}}
    <div class="col-md-4">
        @can('pengaduan.handle')
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <strong><i class="bi bi-tools"></i> Tindak Lanjut</strong>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('pengaduan.tindak-lanjut', $pengaduan) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Ubah Status ke</label>
                        <select name="status_baru" class="form-select" required>
                            @foreach (['baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $v => $l)
                                <option value="{{ $v }}" {{ $pengaduan->status == $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan <span class="text-danger">*</span></label>
                        <textarea name="catatan" class="form-control" rows="4" required
                                  placeholder="Jelaskan tindakan yang dilakukan..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle"></i> Simpan Tindak Lanjut
                    </button>
                </form>
            </div>
        </div>
        @endcan

        {{-- ============ PANEL ESKALASI (BARU) ============ --}}
        @can('pengaduan.handle')
        @if ($pengaduan->tingkat === 'rt' && $pengaduan->status_eskalasi === 'tidak' && auth()->user()->hasAnyRole(['ketua_rt', 'super_admin']))
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-warning text-dark">
                <strong><i class="bi bi-arrow-up-circle"></i> Eskalasi ke RW</strong>
            </div>
            <div class="card-body">
                <p class="small text-muted">
                    Gunakan eskalasi jika masalah ini butuh penanganan RW
                    (misal: butuh dana, fasilitas RW, atau masalah antar-RT).
                </p>
                <form method="POST" action="{{ route('pengaduan.eskalasi', $pengaduan) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Alasan Eskalasi <span class="text-danger">*</span></label>
                        <textarea name="alasan" class="form-control" rows="3" required
                                  placeholder="Kenapa perlu ditangani RW?"></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning w-100"
                            onclick="return confirm('Yakin eskalasi ke RW?')">
                        <i class="bi bi-arrow-up-circle"></i> Eskalasi ke RW
                    </button>
                </form>
            </div>
        </div>
        @endif
        @endcan

        @if ($pengaduan->status_eskalasi !== 'tidak')
        <div class="alert alert-warning mt-3">
            <i class="bi bi-info-circle"></i>
            Pengaduan ini <strong>{{ $pengaduan->status_eskalasi === 'dieskalasi' ? 'telah dieskalasi ke RW' : 'merupakan hasil eskalasi dari RT' }}</strong>.
            @if ($pengaduan->pengaduanAsal)
                <a href="{{ route('pengaduan.show', $pengaduan->pengaduanAsal) }}">Lihat pengaduan asal</a>
            @endif
        </div>
        @endif
        {{-- ============ END PANEL ESKALASI ============ --}}


        {{-- Info Pelapor --}}
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white"><strong><i class="bi bi-person"></i> Pelapor</strong></div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                         style="width: 45px; height: 45px; font-weight: 700;">
                        {{ strtoupper(substr($pengaduan->user->name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $pengaduan->user->name ?? '-' }}</div>
                        <small class="text-muted">{{ $pengaduan->user->no_hp ?? '-' }}</small>
                    </div>
                </div>
                <hr>
                <table class="table table-sm mb-0">
                    <tr><th>RT</th><td>{{ $pengaduan->rt->nama_rt ?? '-' }}</td></tr>
                    <tr><th>Kategori</th><td>{{ ucfirst($pengaduan->kategori) }}</td></tr>
                    <tr><th>Dibuat</th><td>{{ $pengaduan->created_at->format('d M Y') }}</td></tr>
                    @if ($pengaduan->penangan)
                        <tr><th>Ditangani</th><td>{{ $pengaduan->penangan->name }}</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@if ($pengaduan->latitude && $pengaduan->longitude)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const lat = {{ $pengaduan->latitude }};
    const lng = {{ $pengaduan->longitude }};
    const mapDetail = L.map('map-detail').setView([lat, lng], 17);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(mapDetail);
    L.marker([lat, lng]).addTo(mapDetail)
        .bindPopup('{{ $pengaduan->judul }}').openPopup();
</script>
@endif
@endpush

@endsection