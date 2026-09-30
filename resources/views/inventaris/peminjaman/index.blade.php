@extends('layouts.admin')

@section('title', 'Peminjaman Aset')

@section('content')

@if (auth()->user()->hasRole('warga'))
<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    Ingin meminjam aset? Silakan lapor ke <strong>Ketua RT</strong> Anda terlebih dahulu.
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Peminjaman Aset</h5>
        <small class="text-muted">Total: {{ $peminjamans->total() }} peminjaman</small>
    </div>
    @can('inventaris.pinjam')
    <a href="{{ route('peminjaman-aset.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Ajukan Peminjaman
    </a>
    @endcan
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('peminjaman-aset.index') }}" class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach (['diajukan' => 'Diajukan', 'disetujui' => 'Disetujui', 'dipinjam' => 'Dipinjam', 'dikembalikan' => 'Dikembalikan', 'ditolak' => 'Ditolak', 'terlambat' => 'Terlambat'] as $v => $l)
                        <option value="{{ $v }}" {{ request('status') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('peminjaman-aset.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="50">#</th>
                    <th>Kode</th>
                    <th>Aset</th>
                    <th>Peminjam</th>
                    <th class="text-center">Jumlah</th>
                    <th>Pinjam</th>
                    <th>Rencana Kembali</th>
                    <th>Status</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($peminjamans as $i => $p)
                <tr class="{{ $p->terlambat ? 'table-warning' : '' }}">
                    <td>{{ $peminjamans->firstItem() + $i }}</td>
                    <td><code>{{ $p->kode_pinjam }}</code></td>
                    <td><strong>{{ $p->aset->nama ?? '-' }}</strong></td>
                    <td>{{ $p->user->name ?? '-' }}<br><small class="text-muted">{{ $p->rt->nama_rt ?? '-' }}</small></td>
                    <td class="text-center">{{ $p->jumlah }}</td>
                    <td><small>{{ $p->tanggal_pinjam?->format('d M Y') }}</small></td>
                    <td>
                        <small>{{ $p->tanggal_rencana_kembali?->format('d M Y') }}</small>
                        @if ($p->terlambat)
                            <br><span class="badge bg-danger">Terlambat</span>
                        @endif
                    </td>
                    <td><span class="badge {{ $p->status_badge }}">{{ $p->status_label }}</span></td>
                    <td class="text-center">
                        <a href="{{ route('peminjaman-aset.show', $p) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4 text-muted">Belum ada peminjaman.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($peminjamans->hasPages())
    <div class="card-footer bg-white">{{ $peminjamans->links() }}</div>
    @endif
</div>

@endsection