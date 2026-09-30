@extends('layouts.admin')

@section('title', 'Jadwal Ronda')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Jadwal Ronda</h5>
        <small class="text-muted">Total: {{ $jadwals->total() }} jadwal</small>
    </div>
    @can('ronda.schedule')
    <a href="{{ route('ronda.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Buat Jadwal
    </a>
    @endcan
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('ronda.index') }}" class="row g-2">
            <div class="col-md-3">
                <select name="rt_id" class="form-select form-select-sm">
                    <option value="">Semua RT</option>
                    @foreach ($rts as $rt)
                        <option value="{{ $rt->id }}" {{ request('rt_id') == $rt->id ? 'selected' : '' }}>{{ $rt->nama_rt }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach (['draft' => 'Draft', 'aktif' => 'Aktif', 'selesai' => 'Selesai', 'batal' => 'Batal'] as $v => $l)
                        <option value="{{ $v }}" {{ request('status') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ request('tanggal') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('ronda.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
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
                    <th>Tanggal</th>
                    <th>RT</th>
                    <th>Shift</th>
                    <th>Jam</th>
                    <th>Koordinator</th>
                    <th class="text-center">Anggota</th>
                    <th>Status</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jadwals as $i => $j)
                <tr>
                    <td>{{ $jadwals->firstItem() + $i }}</td>
                    <td><strong>{{ $j->tanggal->format('d M Y') }}</strong></td>
                    <td>{{ $j->rt->nama_rt ?? '-' }}</td>
                    <td><span class="badge bg-info text-dark">{{ $j->shift_label }}</span></td>
                    <td>
                        @if ($j->jam_mulai)
                            {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td><small>{{ $j->koordinator_nama ?? $j->koordinator->name ?? '-' }}</small></td>
                    <td class="text-center"><span class="badge bg-primary">{{ $j->anggotas_count }}</span></td>
                    <td><span class="badge {{ $j->status_badge }}">{{ ucfirst($j->status) }}</span></td>
                    <td class="text-center">
                        <a href="{{ route('ronda.show', $j) }}" class="btn btn-sm btn-outline-info" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada jadwal ronda.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($jadwals->hasPages())
    <div class="card-footer bg-white">{{ $jadwals->links() }}</div>
    @endif
</div>

@endsection