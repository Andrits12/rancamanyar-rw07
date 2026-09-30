@extends('layouts.admin')

@section('title', 'Daftar Lamaran')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">
            @if (auth()->user()->hasRole('warga'))
            Lamaran Saya
            @else
            Daftar Lamaran
            @endif
        </h5>
        <small class="text-muted">Total: {{ $lamarans->total() }} lamaran</small>
    </div>
    @if (auth()->user()->hasRole('warga'))
    <a href="{{ route('lowongan.index') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-briefcase"></i> Cari Lowongan
    </a>
    @endif
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('lamaran.index') }}" class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach (['diajukan' => 'Diajukan', 'diverifikasi_rt' => 'Diverifikasi RT', 'diteruskan' => 'Diteruskan', 'interview' => 'Interview', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'] as $v => $l)
                        <option value="{{ $v }}" {{ request('status') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('lamaran.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>
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
                    <th>Pelamar</th>
                    <th>Lowongan</th>
                    <th>RT</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lamarans as $i => $l)
                <tr>
                    <td>{{ $lamarans->firstItem() + $i }}</td>
                    <td><code>{{ $l->kode_lamaran }}</code></td>
                    <td>
                        <strong>{{ $l->user->name ?? '-' }}</strong>
                        <br><small class="text-muted">{{ $l->user->no_hp ?? '-' }}</small>
                    </td>
                    <td>
                        <a href="{{ route('lowongan.show', $l->lowongan_id) }}" class="text-decoration-none">
                            {{ Str::limit($l->lowongan->judul ?? '-', 30) }}
                        </a>
                        <br><small class="text-muted">{{ $l->lowongan->perusahaan ?? '-' }}</small>
                    </td>
                    <td>{{ $l->rt->nama_rt ?? '-' }}</td>
                    <td><small>{{ $l->created_at->format('d M Y') }}</small></td>
                    <td>
                        <span class="badge {{ $l->status_badge }}">{{ $l->status_label }}</span>
                        @if ($l->is_direkomendasikan)
                            <br><small class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> Direkomendasikan</small>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('lamaran.show', $l) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada lamaran.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($lamarans->hasPages())
    <div class="card-footer bg-white">{{ $lamarans->links() }}</div>
    @endif
</div>

@endsection