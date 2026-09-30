@extends('layouts.admin')

@section('title', 'Detail Kematian: ' . ($kematian->warga->nama ?? '-'))

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">🕊️ Detail Kematian</h5>
        <small class="text-muted">Kode: <code>{{ $kematian->kode_kematian }}</code></small>
    </div>
    <div>
        @can('akta.edit')
        <a href="{{ route('kematian.edit', $kematian) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endcan
        <a href="{{ route('kematian.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center mb-3"
                     style="width: 80px; height: 80px; font-size: 40px;">🕊️</div>
                <h5 class="mb-1">{{ $kematian->warga->nama ?? '-' }}</h5>
                <p class="text-muted small mb-2">{{ $kematian->warga->nik ?? '-' }}</p>
                <span class="badge bg-secondary">{{ $kematian->sebab_label }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Data Kematian</strong></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th width="180">Kode</th><td><code>{{ $kematian->kode_kematian }}</code></td></tr>
                    <tr><th>Nama</th><td><strong>{{ $kematian->warga->nama ?? '-' }}</strong></td></tr>
                    <tr><th>NIK</th><td>{{ $kematian->warga->nik ?? '-' }}</td></tr>
                    <tr><th>Jenis Kelamin</th><td>{{ $kematian->warga->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                    <tr><th>Umur</th><td>{{ $kematian->warga->umur ?? '-' }} tahun</td></tr>
                    <tr><th>Tanggal Meninggal</th><td>{{ $kematian->tanggal_meninggal->format('d F Y') }} {{ $kematian->jam_meninggal ? '• ' . $kematian->jam_meninggal : '' }}</td></tr>
                    <tr><th>Tempat</th><td>{{ $kematian->tempat_meninggal ?? '-' }}</td></tr>
                    <tr><th>Sebab</th><td>{{ $kematian->sebab_label }} @if($kematian->keterangan_sebab) — {{ $kematian->keterangan_sebab }} @endif</td></tr>
                    <tr><th>RT</th><td>{{ $kematian->rt->nama_rt ?? '-' }}</td></tr>
                </table>

                <hr>
                <h6>Pemakaman</h6>
                <table class="table table-sm mb-0">
                    <tr><th width="180">Tempat</th><td>{{ $kematian->tempat_pemakaman ?? '-' }}</td></tr>
                    <tr><th>Tanggal</th><td>{{ $kematian->tanggal_pemakaman?->format('d F Y') ?? '-' }}</td></tr>
                </table>

                <hr>
                <h6>Dokumen Akta</h6>
                @if ($kematian->no_akta_kematian)
                    <p class="mb-1"><strong>No Akta:</strong> {{ $kematian->no_akta_kematian }}</p>
                    <p class="mb-1"><strong>Tanggal:</strong> {{ $kematian->tanggal_akta?->format('d F Y') ?? '-' }}</p>
                    @if ($kematian->dokumen_akta)
                        <a href="{{ route('file.preview', $kematian->dokumen_akta) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-file-earmark-pdf"></i> Lihat Dokumen
                        </a>
                    @endif
                @else
                    <p class="text-muted mb-0">Belum ada akta.</p>
                @endif

                @if ($kematian->keterangan)
                    <hr>
                    <h6>Keterangan</h6>
                    <p class="mb-0">{{ $kematian->keterangan }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
