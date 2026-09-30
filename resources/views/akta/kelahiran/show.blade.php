@extends('layouts.admin')

@section('title', 'Detail Kelahiran: ' . $kelahiran->nama_bayi)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">👶 Detail Kelahiran</h5>
        <small class="text-muted">Kode: <code>{{ $kelahiran->kode_kelahiran }}</code></small>
    </div>
    <div>
        @can('akta.edit')
        <a href="{{ route('kelahiran.edit', $kelahiran) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endcan
        <a href="{{ route('kelahiran.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
                     style="width: 80px; height: 80px; font-size: 40px;">
                    {{ $kelahiran->jenis_kelamin === 'L' ? '👦' : '👧' }}
                </div>
                <h5 class="mb-1">{{ $kelahiran->nama_bayi }}</h5>
                <p class="text-muted small mb-2">{{ $kelahiran->jenis_kelamin_label }}</p>
                <span class="badge {{ $kelahiran->kondisi_badge }}">{{ ucfirst($kelahiran->kondisi_lahir) }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Data Kelahiran</strong></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th width="180">Kode</th><td><code>{{ $kelahiran->kode_kelahiran }}</code></td></tr>
                    <tr><th>Nama Bayi</th><td><strong>{{ $kelahiran->nama_bayi }}</strong></td></tr>
                    <tr><th>Jenis Kelamin</th><td>{{ $kelahiran->jenis_kelamin_label }}</td></tr>
                    <tr><th>Tanggal Lahir</th><td>{{ $kelahiran->tanggal_lahir->format('d F Y') }} {{ $kelahiran->jam_lahir ? '• ' . $kelahiran->jam_lahir : '' }}</td></tr>
                    <tr><th>Tempat Lahir</th><td>{{ $kelahiran->tempat_lahir ?? '-' }}</td></tr>
                    <tr><th>Berat / Panjang</th><td>{{ $kelahiran->berat_lahir ?? '-' }} kg / {{ $kelahiran->panjang_lahir ?? '-' }} cm</td></tr>
                    <tr><th>Kondisi</th><td>{{ ucfirst($kelahiran->kondisi_lahir) }}</td></tr>
                    <tr><th>Ayah</th><td>{{ $kelahiran->nama_ayah }} @if($kelahiran->nik_ayah) ({{ $kelahiran->nik_ayah }}) @endif</td></tr>
                    <tr><th>Ibu</th><td>{{ $kelahiran->nama_ibu }} @if($kelahiran->nik_ibu) ({{ $kelahiran->nik_ibu }}) @endif</td></tr>
                    <tr><th>RT</th><td>{{ $kelahiran->rt->nama_rt ?? '-' }}</td></tr>
                    @if ($kelahiran->keluarga)
                        <tr><th>Keluarga</th><td>{{ $kelahiran->keluarga->no_kk }} — {{ $kelahiran->keluarga->kepala_keluarga_nama }}</td></tr>
                    @endif
                </table>

                <hr>
                <h6>Dokumen Akta</h6>
                @if ($kelahiran->no_akta_kelahiran)
                    <p class="mb-1"><strong>No Akta:</strong> {{ $kelahiran->no_akta_kelahiran }}</p>
                    <p class="mb-1"><strong>Tanggal Akta:</strong> {{ $kelahiran->tanggal_akta?->format('d F Y') ?? '-' }}</p>
                    @if ($kelahiran->dokumen_akta)
                        <a href="{{ route('file.preview', $kelahiran->dokumen_akta) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-file-earmark-pdf"></i> Lihat Dokumen
                        </a>
                    @endif
                @else
                    <p class="text-muted mb-0">Belum ada akta.</p>
                @endif

                @if ($kelahiran->keterangan)
                    <hr>
                    <h6>Keterangan</h6>
                    <p class="mb-0">{{ $kelahiran->keterangan }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection