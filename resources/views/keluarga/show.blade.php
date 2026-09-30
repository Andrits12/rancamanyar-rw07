@extends('layouts.admin')

@section('title', 'Detail Keluarga: ' . $keluarga->kepala_keluarga_nama)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Detail Keluarga</h5>
        <small class="text-muted">No KK: <code>{{ $keluarga->no_kk }}</code></small>
    </div>
    <div>
        @can('keluarga.edit')
        <a href="{{ route('keluarga.edit', $keluarga) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit KK
        </a>
        @endcan
        <a href="{{ route('keluarga.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
                     style="width: 80px; height: 80px; font-size: 32px; font-weight: 700;">
                    {{ strtoupper(substr($keluarga->kepala_keluarga_nama, 0, 1)) }}
                </div>
                <h5 class="mb-1">{{ $keluarga->kepala_keluarga_nama }}</h5>
                <p class="text-muted small mb-2">Kepala Keluarga</p>
                <span class="badge bg-info text-dark">{{ $keluarga->wargas->count() }} anggota</span>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Informasi Keluarga</strong></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th width="200">No KK</th><td><code>{{ $keluarga->no_kk }}</code></td></tr>
                    <tr><th>Kepala Keluarga</th><td>{{ $keluarga->kepala_keluarga_nama }}</td></tr>
                    <tr><th>RT</th><td>{{ $keluarga->rt->nama_rt ?? '-' }}</td></tr>
                    <tr><th>Alamat</th><td>{{ $keluarga->alamat }}</td></tr>
                    <tr><th>Status Rumah</th><td>{{ $keluarga->status_rumah ? ucwords(str_replace('_', ' ', $keluarga->status_rumah)) : '-' }}</td></tr>
                    <tr><th>Status Keluarga</th><td>{{ ucfirst($keluarga->status_keluarga) }}</td></tr>
                    <tr><th>Tanggal Daftar</th><td>{{ $keluarga->tgl_daftar?->format('d F Y') ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Anggota Keluarga --}}
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong><i class="bi bi-people"></i> Anggota Keluarga ({{ $keluarga->wargas->count() }})</strong>
        @can('warga.create')
        <a href="{{ route('warga.create', ['keluarga_id' => $keluarga->id]) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Anggota
        </a>
        @endcan
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>JK</th>
                    <th>Umur</th>
                    <th>Status Keluarga</th>
                    <th>Status Hidup</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($keluarga->wargas as $w)
                <tr>
                    <td><code>{{ $w->nik }}</code></td>
                    <td><strong>{{ $w->nama }}</strong></td>
                    <td>{{ $w->jenis_kelamin == 'L' ? 'L' : 'P' }}</td>
                    <td>{{ $w->umur }} th</td>
                    <td>{{ ucwords(str_replace('_', ' ', $w->status_keluarga)) }}</td>
                    <td>
                        @if ($w->status_hidup === 'hidup')
                            <span class="badge bg-success">Hidup</span>
                        @elseif ($w->status_hidup === 'meninggal')
                            <span class="badge bg-dark">Meninggal</span>
                        @else
                            <span class="badge bg-warning text-dark">Pindah</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('warga.show', $w) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                        @can('warga.edit')
                        <a href="{{ route('warga.edit', $w) }}" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        Belum ada anggota keluarga. Klik "Tambah Anggota" untuk menambahkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection