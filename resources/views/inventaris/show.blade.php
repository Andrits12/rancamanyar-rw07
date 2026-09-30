@extends('layouts.admin')

@section('title', 'Detail Aset: ' . $aset->nama)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">{{ $aset->nama }}</h5>
        <small class="text-muted">Kode: <code>{{ $aset->kode_aset }}</code></small>
    </div>
    <div>
        @can('inventaris.pinjam')
        <a href="{{ route('peminjaman-aset.create', ['aset_id' => $aset->id]) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-box-arrow-up"></i> Pinjam Aset
        </a>
        @endcan
        @can('inventaris.edit')
        <a href="{{ route('inventaris.edit', $aset) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endcan
        <a href="{{ route('inventaris.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            @if ($aset->foto_url)
                <img src="{{ $aset->foto_url }}" class="card-img-top" style="max-height: 250px; object-fit: cover;">
            @else
                <div class="d-flex align-items-center justify-content-center bg-secondary bg-opacity-10" style="height: 200px;">
                    <i class="bi bi-box-seam" style="font-size: 60px; color: #cbd5e1;"></i>
                </div>
            @endif
            <div class="card-body text-center">
                <span class="badge bg-info text-dark">{{ $aset->kategori_label }}</span>
                <span class="badge {{ $aset->kondisi_badge }}">{{ ucwords(str_replace('_', ' ', $aset->kondisi)) }}</span>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body text-center">
                <div class="row">
                    <div class="col-4">
                        <h4 class="mb-0">{{ $aset->jumlah_total }}</h4>
                        <small class="text-muted">Total</small>
                    </div>
                    <div class="col-4">
                        <h4 class="mb-0 text-warning">{{ $aset->jumlah_dipinjam }}</h4>
                        <small class="text-muted">Dipinjam</small>
                    </div>
                    <div class="col-4">
                        <h4 class="mb-0 text-success">{{ $aset->jumlah_tersedia }}</h4>
                        <small class="text-muted">Tersedia</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Info Aset</strong></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th width="180">Kode Aset</th><td><code>{{ $aset->kode_aset }}</code></td></tr>
                    <tr><th>Nama</th><td>{{ $aset->nama }}</td></tr>
                    <tr><th>Kategori</th><td>{{ $aset->kategori_label }}</td></tr>
                    <tr><th>Jumlah</th><td>{{ $aset->jumlah_total }} {{ $aset->satuan }}</td></tr>
                    <tr><th>Kondisi</th><td>{{ ucwords(str_replace('_', ' ', $aset->kondisi)) }}</td></tr>
                    <tr><th>Lokasi</th><td>{{ $aset->lokasi_penyimpanan ?? '-' }}</td></tr>
                    <tr><th>Tanggal Perolehan</th><td>{{ $aset->tanggal_perolehan?->format('d F Y') ?? '-' }}</td></tr>
                    <tr><th>Harga</th><td>{{ $aset->harga_perolehan ? 'Rp ' . number_format($aset->harga_perolehan, 0, ',', '.') : '-' }}</td></tr>
                    <tr><th>Sumber</th><td>{{ $aset->sumber_perolehan ?? '-' }}</td></tr>
                    <tr><th>Status</th><td>
                        @if ($aset->is_active) <span class="badge bg-success">Aktif</span>
                        @else <span class="badge bg-secondary">Nonaktif</span> @endif
                    </td></tr>
                </table>

                @if ($aset->deskripsi)
                    <hr>
                    <p class="mb-0">{{ $aset->deskripsi }}</p>
                @endif
            </div>
        </div>

        {{-- Riwayat Peminjaman --}}
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white"><strong><i class="bi bi-clock-history"></i> Riwayat Peminjaman</strong></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Peminjam</th>
                            <th class="text-center">Jumlah</th>
                            <th>Pinjam</th>
                            <th>Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($aset->peminjamans->sortByDesc('created_at')->take(10) as $p)
                        <tr>
                            <td><code>{{ $p->kode_pinjam }}</code></td>
                            <td>{{ $p->user->name ?? '-' }}<br><small class="text-muted">{{ $p->rt->nama_rt ?? '-' }}</small></td>
                            <td class="text-center">{{ $p->jumlah }}</td>
                            <td><small>{{ $p->tanggal_pinjam?->format('d M Y') }}</small></td>
                            <td><small>{{ $p->tanggal_kembali_aktual?->format('d M Y') ?? '-' }}</small></td>
                            <td><span class="badge {{ $p->status_badge }}">{{ $p->status_label }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-3 text-muted">Belum ada riwayat peminjaman.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection