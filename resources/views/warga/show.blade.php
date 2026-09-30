@extends('layouts.admin')

@section('title', 'Detail Warga: ' . $warga->nama)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Detail Warga</h5>
        <small class="text-muted">NIK: <code>{{ $warga->nik }}</code></small>
    </div>
    <div>
        @can('warga.edit')
        <a href="{{ route('warga.edit', $warga) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endcan
        <a href="{{ route('warga.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
                     style="width: 80px; height: 80px; font-size: 32px; font-weight: 700;">
                    {{ strtoupper(substr($warga->nama, 0, 1)) }}
                </div>
                <h5 class="mb-1">{{ $warga->nama }}</h5>
                <p class="text-muted small mb-2">{{ ucwords(str_replace('_', ' ', $warga->status_keluarga)) }}</p>
                <span class="badge bg-info text-dark">{{ $warga->umur }} tahun • {{ $warga->kategori_umur }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Data Pribadi</strong></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th width="200">NIK</th><td><code>{{ $warga->nik }}</code></td></tr>
                    <tr><th>Nama</th><td>{{ $warga->nama }}</td></tr>
                    <tr><th>Jenis Kelamin</th><td>{{ $warga->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                    <tr><th>Tempat / Tgl Lahir</th><td>{{ $warga->tempat_lahir ?? '-' }}, {{ $warga->tgl_lahir?->format('d F Y') }}</td></tr>
                    <tr><th>Umur</th><td>{{ $warga->umur }} tahun ({{ $warga->kategori_umur }})</td></tr>
                    <tr><th>Agama</th><td>{{ $warga->agama ?? '-' }}</td></tr>
                    <tr><th>Pendidikan</th><td>{{ $warga->pendidikan ?? '-' }}</td></tr>
                    <tr><th>Pekerjaan</th><td>{{ $warga->pekerjaan ?? '-' }}</td></tr>
                    <tr><th>Status Kawin</th><td>{{ $warga->status_kawin ?? '-' }}</td></tr>
                    <tr><th>Golongan Darah</th><td>{{ $warga->golongan_darah ?? '-' }}</td></tr>
                    <tr><th>Kewarganegaraan</th><td>{{ $warga->kewarganegaraan }}</td></tr>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white"><strong>Data Keluarga</strong></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th width="200">No KK</th><td><code>{{ $warga->keluarga->no_kk ?? '-' }}</code></td></tr>
                    <tr><th>Kepala Keluarga</th><td>
                        <a href="{{ route('keluarga.show', $warga->keluarga_id) }}">
                            {{ $warga->keluarga->kepala_keluarga_nama ?? '-' }}
                        </a>
                    </td></tr>
                    <tr><th>Status dalam Keluarga</th><td>{{ ucwords(str_replace('_', ' ', $warga->status_keluarga)) }}</td></tr>
                    <tr><th>RT</th><td>{{ $warga->rt->nama_rt ?? '-' }}</td></tr>
                    <tr><th>Alamat</th><td>{{ $warga->keluarga->alamat ?? '-' }}</td></tr>
                    <tr><th>Status Hidup</th><td>
                        @if ($warga->status_hidup === 'hidup')
                            <span class="badge bg-success">Hidup</span>
                        @elseif ($warga->status_hidup === 'meninggal')
                            <span class="badge bg-dark">Meninggal</span>
                        @else
                            <span class="badge bg-warning text-dark">Pindah</span>
                        @endif
                    </td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection