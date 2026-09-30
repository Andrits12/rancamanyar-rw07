@extends('layouts.admin')

@section('title', 'Data Warga')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Data Warga</h5>
        <small class="text-muted">Total: {{ $wargas->total() }} warga</small>
    </div>
    @can('warga.create')
    <a href="{{ route('warga.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Warga
    </a>
    @endcan
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('warga.index') }}" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Cari NIK / Nama..." value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <select name="rt_id" class="form-select form-select-sm">
                    <option value="">Semua RT</option>
                    @foreach ($rts as $rt)
                        <option value="{{ $rt->id }}" {{ request('rt_id') == $rt->id ? 'selected' : '' }}>{{ $rt->nama_rt }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="jk" class="form-select form-select-sm">
                    <option value="">Semua JK</option>
                    <option value="L" {{ request('jk') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ request('jk') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('warga.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
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
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>JK</th>
                    <th>Umur</th>
                    <th>RT</th>
                    <th>Keluarga</th>
                    <th>Status</th>
                    <th width="120" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($wargas as $i => $w)
                <tr>
                    <td>{{ $wargas->firstItem() + $i }}</td>
                    <td><code>{{ $w->nik }}</code></td>
                    <td>
                        <strong>{{ $w->nama }}</strong>
                        <br><small class="text-muted">{{ ucwords(str_replace('_', ' ', $w->status_keluarga)) }}</small>
                    </td>
                    <td>{{ $w->jenis_kelamin == 'L' ? 'L' : 'P' }}</td>
                    <td>{{ $w->umur }} th</td>
                    <td>{{ $w->rt->nama_rt ?? '-' }}</td>
                    <td>
                        <a href="{{ route('keluarga.show', $w->keluarga_id) }}" class="text-decoration-none">
                            {{ $w->keluarga->kepala_keluarga_nama ?? '-' }}
                        </a>
                    </td>
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
                    <td colspan="9" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada data warga.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($wargas->hasPages())
    <div class="card-footer bg-white">{{ $wargas->links() }}</div>
    @endif
</div>

@endsection