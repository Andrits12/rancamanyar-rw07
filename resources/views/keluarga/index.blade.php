@extends('layouts.admin')

@section('title', 'Data Keluarga')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Data Keluarga (KK)</h5>
        <small class="text-muted">Total: {{ $keluargas->total() }} keluarga</small>
    </div>
    @can('keluarga.create')
    <a href="{{ route('keluarga.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Keluarga
    </a>
    @endcan
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('keluarga.index') }}" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Cari No KK / Kepala Keluarga / Alamat..."
                       value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <select name="rt_id" class="form-select form-select-sm">
                    <option value="">Semua RT</option>
                    @foreach ($rts as $rt)
                        <option value="{{ $rt->id }}" {{ request('rt_id') == $rt->id ? 'selected' : '' }}>
                            {{ $rt->nama_rt }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="pindah" {{ request('status') == 'pindah' ? 'selected' : '' }}>Pindah</option>
                    <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('keluarga.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Reset
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
                    <th>No KK</th>
                    <th>Kepala Keluarga</th>
                    <th>RT</th>
                    <th>Alamat</th>
                    <th class="text-center">Anggota</th>
                    <th>Status</th>
                    <th width="150" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($keluargas as $i => $kk)
                <tr>
                    <td>{{ $keluargas->firstItem() + $i }}</td>
                    <td><code>{{ $kk->no_kk }}</code></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                 style="width: 36px; height: 36px; font-weight: 700;">
                                {{ strtoupper(substr($kk->kepala_keluarga_nama, 0, 1)) }}
                            </div>
                            <strong>{{ $kk->kepala_keluarga_nama }}</strong>
                        </div>
                    </td>
                    <td>{{ $kk->rt->nama_rt ?? '-' }}</td>
                    <td><small>{{ Str::limit($kk->alamat, 40) }}</small></td>
                    <td class="text-center">
                        <span class="badge bg-info text-dark">{{ $kk->wargas_count }} orang</span>
                    </td>
                    <td>
                        @if ($kk->status_keluarga === 'aktif')
                            <span class="badge bg-success">Aktif</span>
                        @elseif ($kk->status_keluarga === 'pindah')
                            <span class="badge bg-warning text-dark">Pindah</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('keluarga.show', $kk) }}" class="btn btn-sm btn-outline-info" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                        @can('keluarga.edit')
                        <a href="{{ route('keluarga.edit', $kk) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endcan
                        @can('keluarga.delete')
                        <form method="POST" action="{{ route('keluarga.destroy', $kk) }}" class="d-inline"
                              onsubmit="return confirm('Yakin hapus keluarga {{ $kk->kepala_keluarga_nama }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada data keluarga.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($keluargas->hasPages())
    <div class="card-footer bg-white">
        {{ $keluargas->links() }}
    </div>
    @endif
</div>

@endsection