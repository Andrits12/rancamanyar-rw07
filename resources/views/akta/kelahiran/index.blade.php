@extends('layouts.admin')

@section('title', 'Data Kelahiran')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">👶 Data Kelahiran</h5>
        <small class="text-muted">Total: {{ $kelahirans->total() }} data</small>
    </div>
    @can('akta.create')
    <a href="{{ route('kelahiran.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Catat Kelahiran
    </a>
    @endcan
</div>

{{-- Statistik --}}
<div class="row g-2 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Bulan Ini</small>
                <h5 class="mb-0 text-primary">{{ $stats['bulan_ini'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Tahun {{ now()->year }}</small>
                <h5 class="mb-0 text-success">{{ $stats['tahun_ini'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Total Tercatat</small>
                <h5 class="mb-0">{{ $stats['total'] }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('kelahiran.index') }}" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Cari nama bayi..." value="{{ request('q') }}">
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
                <input type="number" name="tahun" class="form-control form-control-sm"
                       placeholder="Tahun" value="{{ request('tahun') }}" min="2000" max="{{ now()->year }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('kelahiran.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
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
                    <th>Kode</th>
                    <th>Nama Bayi</th>
                    <th>JK</th>
                    <th>Tgl Lahir</th>
                    <th>Orang Tua</th>
                    <th>RT</th>
                    <th>Akta</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kelahirans as $i => $k)
                <tr>
                    <td>{{ $kelahirans->firstItem() + $i }}</td>
                    <td><code>{{ $k->kode_kelahiran }}</code></td>
                    <td>
                        <strong>{{ $k->nama_bayi }}</strong>
                        @if ($k->kondisi_lahir !== 'normal')
                            <br><span class="badge {{ $k->kondisi_badge }}">{{ ucfirst($k->kondisi_lahir) }}</span>
                        @endif
                    </td>
                    <td>{{ $k->jenis_kelamin === 'L' ? '👦 L' : '👧 P' }}</td>
                    <td>
                        <small>{{ $k->tanggal_lahir->format('d M Y') }}</small>
                        @if ($k->berat_lahir)
                            <br><small class="text-muted">{{ $k->berat_lahir }} kg / {{ $k->panjang_lahir }} cm</small>
                        @endif
                    </td>
                    <td>
                        <small>Ayah: {{ $k->nama_ayah }}</small><br>
                        <small>Ibu: {{ $k->nama_ibu }}</small>
                    </td>
                    <td>{{ $k->rt->nama_rt ?? '-' }}</td>
                    <td>
                        @if ($k->no_akta_kelahiran)
                            <span class="badge bg-success"><i class="bi bi-check"></i> Ada</span>
                        @else
                            <span class="badge bg-secondary">Belum</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('kelahiran.show', $k) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada data kelahiran.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($kelahirans->hasPages())
    <div class="card-footer bg-white">{{ $kelahirans->links() }}</div>
    @endif
</div>

@endsection