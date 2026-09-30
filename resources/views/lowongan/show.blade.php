@extends('layouts.admin')

@section('title', $lowongan->judul)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">{{ $lowongan->judul }}</h5>
        <small class="text-muted">
            <i class="bi bi-building"></i> {{ $lowongan->perusahaan }}
            • <i class="bi bi-eye"></i> {{ $lowongan->views }} views
        </small>
    </div>
    <div>
        @can('lowongan.edit')
        <a href="{{ route('lowongan.edit', $lowongan) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endcan
        @can('lowongan.verifikasi')
        <a href="{{ route('lamaran.index', ['lowongan_id' => $lowongan->id]) }}" class="btn btn-info btn-sm">
            <i class="bi bi-people"></i> Lihat Pelamar ({{ $lowongan->lamarans->count() }})
        </a>
        @endcan
        <a href="{{ route('lowongan.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge {{ $lowongan->status_badge }}">{{ ucfirst($lowongan->status) }}</span>
                    <span class="badge bg-info text-dark">{{ $lowongan->jenis_label }}</span>
                    @if ($lowongan->is_pinned)
                        <span class="badge bg-warning text-dark"><i class="bi bi-pin-angle-fill"></i> Pinned</span>
                    @endif
                </div>

                <table class="table table-sm">
                    <tr><th width="180">Posisi</th><td><strong>{{ $lowongan->judul }}</strong></td></tr>
                    <tr><th>Perusahaan</th><td>{{ $lowongan->perusahaan }}</td></tr>
                    <tr><th>Lokasi</th><td>{{ $lowongan->lokasi ?? '-' }}</td></tr>
                    <tr><th>Gaji</th><td>{{ $lowongan->gaji_range }}</td></tr>
                    <tr><th>Dibuka</th><td>{{ $lowongan->tanggal_buka?->format('d F Y') ?? '-' }}</td></tr>
                    <tr><th>Deadline</th><td>
                        {{ $lowongan->deadline?->format('d F Y') ?? 'Tidak disebutkan' }}
                        @if ($lowongan->deadline && $lowongan->sisa_hari > 0)
                            <span class="badge bg-warning text-dark ms-2">{{ $lowongan->sisa_hari }} hari lagi</span>
                        @elseif ($lowongan->deadline && $lowongan->sisa_hari == 0)
                            <span class="badge bg-danger ms-2">Ditutup</span>
                        @endif
                    </td></tr>
                </table>

                <hr>
                <h6>Deskripsi Pekerjaan</h6>
                <p style="white-space: pre-line;">{{ $lowongan->deskripsi }}</p>

                @if ($lowongan->kualifikasi)
                    <hr>
                    <h6>Kualifikasi</h6>
                    <p style="white-space: pre-line;">{{ $lowongan->kualifikasi }}</p>
                @endif

                @if ($lowongan->tanggung_jawab)
                    <hr>
                    <h6>Tanggung Jawab</h6>
                    <p style="white-space: pre-line;">{{ $lowongan->tanggung_jawab }}</p>
                @endif

                @if ($lowongan->keterangan)
                    <hr>
                    <h6>Keterangan</h6>
                    <p style="white-space: pre-line;">{{ $lowongan->keterangan }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        {{-- Tombol Lamar --}}
        @if (auth()->user()->hasRole('warga'))
            @if ($sudahLamar)
                <div class="alert alert-info">
                    <i class="bi bi-check-circle"></i> Anda sudah melamar lowongan ini.
                    <a href="{{ route('lamaran.index') }}">Lihat lamaran</a>
                </div>
            @elseif ($lowongan->status === 'aktif')
                @can('lowongan.lamar')
                <a href="{{ route('lamaran.create', ['lowongan_id' => $lowongan->id]) }}" class="btn btn-primary btn-lg w-100 mb-3">
                    <i class="bi bi-send"></i> Lamar Sekarang
                </a>
                @endcan
            @else
                <div class="alert alert-secondary">Lowongan tidak aktif.</div>
            @endif
        @endif

        {{-- Info Kontak --}}
        @if ($lowongan->kontak_nama || $lowongan->kontak_hp || $lowongan->kontak_email)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong><i class="bi bi-telephone"></i> Kontak HRD</strong></div>
            <div class="card-body">
                @if ($lowongan->kontak_nama)
                    <div class="mb-2"><strong>{{ $lowongan->kontak_nama }}</strong></div>
                @endif
                @if ($lowongan->kontak_hp)
                    <a href="tel:{{ $lowongan->kontak_hp }}" class="btn btn-success btn-sm w-100 mb-2">
                        <i class="bi bi-telephone"></i> {{ $lowongan->kontak_hp }}
                    </a>
                @endif
                @if ($lowongan->kontak_email)
                    <a href="mailto:{{ $lowongan->kontak_email }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-envelope"></i> Email HRD
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Statistik --}}
        @can('lowongan.verifikasi')
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $lowongan->lamarans->count() }}</h3>
                <small class="text-muted">Total Pelamar</small>
                <hr>
                <div class="row text-center small">
                    <div class="col-4">
                        <strong>{{ $lowongan->lamarans->where('status', 'diajukan')->count() }}</strong>
                        <div class="text-muted">Baru</div>
                    </div>
                    <div class="col-4">
                        <strong>{{ $lowongan->lamarans->where('status', 'diteruskan')->count() }}</strong>
                        <div class="text-muted">Diteruskan</div>
                    </div>
                    <div class="col-4">
                        <strong>{{ $lowongan->lamarans->where('status', 'diterima')->count() }}</strong>
                        <div class="text-muted">Diterima</div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>
</div>

@endsection