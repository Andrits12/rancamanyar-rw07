@extends('layouts.admin')

@section('title', 'Panic Button')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">🚨 Panic Button</h5>
        <small class="text-muted">Total: {{ $panicButtons->total() }} laporan darurat</small>
    </div>
    @can('panic.create')
    <a href="{{ route('panic.create') }}" class="btn btn-danger">
        <i class="bi bi-exclamation-triangle"></i> Kirim Sinyal Darurat
    </a>
    @endcan
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('panic.index') }}" class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach (['baru' => 'Baru', 'ditangani' => 'Ditangani', 'selesai' => 'Selesai', 'false_alarm' => 'False Alarm'] as $v => $l)
                        <option value="{{ $v }}" {{ request('status') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="jenis" class="form-select form-select-sm">
                    <option value="">Semua Jenis</option>
                    @foreach (['kebakaran' => 'Kebakaran', 'medis' => 'Medis', 'kriminal' => 'Kriminal', 'bencana' => 'Bencana', 'lainnya' => 'Lainnya'] as $v => $l)
                        <option value="{{ $v }}" {{ request('jenis') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('panic.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="50">#</th>
                    <th>Jenis</th>
                    <th>Pelapor</th>
                    <th>RT</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Waktu</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($panicButtons as $i => $p)
                <tr class="{{ $p->status === 'baru' ? 'table-danger' : '' }}">
                    <td>{{ $panicButtons->firstItem() + $i }}</td>
                    <td><strong>{{ $p->jenis_label }}</strong></td>
                    <td>{{ $p->user->name ?? '-' }}<br><small class="text-muted">{{ $p->user->no_hp ?? '' }}</small></td>
                    <td>{{ $p->rt->nama_rt ?? '-' }}</td>
                    <td><small>{{ Str::limit($p->alamat_lokasi ?? $p->keterangan, 50) }}</small></td>
                    <td><span class="badge {{ $p->status_badge }}">{{ ucfirst(str_replace('_', ' ', $p->status)) }}</span></td>
                    <td><small>{{ $p->created_at->format('d M Y H:i') }}</small></td>
                    <td class="text-center">
                        <a href="{{ route('panic.show', $p) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="bi bi-shield-check" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Tidak ada laporan darurat. Aman! ✅</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($panicButtons->hasPages())
    <div class="card-footer bg-white">{{ $panicButtons->links() }}</div>
    @endif
</div>

@endsection