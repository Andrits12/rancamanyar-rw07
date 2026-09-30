@extends('layouts.admin')

@section('title', 'Surat Menyurat')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Surat Menyurat</h5>
        <small class="text-muted">Total: {{ $surats->total() }} surat</small>
    </div>
    @can('surat.create')
    <a href="{{ route('surat.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Ajukan Surat
    </a>
    @endcan
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('surat.index') }}" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Cari kode surat / keperluan..." value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <select name="jenis" class="form-select form-select-sm">
                    <option value="">Semua Jenis Surat</option>
                    @foreach ($jenisSurats as $js)
                        <option value="{{ $js->id }}" {{ request('jenis') == $js->id ? 'selected' : '' }}>
                            {{ $js->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach (['diajukan' => 'Diajukan', 'verifikasi_rt' => 'Verifikasi RT', 'verifikasi_rw' => 'Verifikasi RW', 'selesai' => 'Selesai', 'ditolak_rt' => 'Ditolak RT', 'ditolak_rw' => 'Ditolak RW'] as $v => $l)
                        <option value="{{ $v }}" {{ request('status') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('surat.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
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
                    <th>Kode Surat</th>
                    <th>Jenis Surat</th>
                    <th>Pemohon</th>
                    <th>Keperluan</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($surats as $i => $s)
                <tr>
                    <td>{{ $surats->firstItem() + $i }}</td>
                    <td><code>{{ $s->kode_surat }}</code></td>
                    <td><strong>{{ $s->jenisSurat->nama ?? '-' }}</strong></td>
                    <td>
                        {{ $s->user->name ?? '-' }}
                        <br><small class="text-muted">{{ $s->rt->nama_rt ?? '-' }}</small>
                    </td>
                    <td><small>{{ Str::limit($s->keperluan, 50) }}</small></td>
                    <td><span class="badge {{ $s->status_badge }}">{{ $s->status_label }}</span></td>
                    <td><small>{{ $s->created_at->format('d M Y') }}</small></td>
                    <td class="text-center">
                        <a href="{{ route('surat.show', $s) }}" class="btn btn-sm btn-outline-info" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada pengajuan surat.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($surats->hasPages())
    <div class="card-footer bg-white">{{ $surats->links() }}</div>
    @endif
</div>

@endsection