@extends('layouts.admin')

@section('title', 'Data Kematian')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">🕊️ Data Kematian</h5>
        <small class="text-muted">Total: {{ $kematians->total() }} data</small>
    </div>
    @can('akta.create')
    <a href="{{ route('kematian.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Catat Kematian
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
                <small class="text-muted">Total</small>
                <h5 class="mb-0">{{ $stats['total'] }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('kematian.index') }}" class="row g-2">
            <div class="col-md-3">
                <select name="rt_id" class="form-select form-select-sm">
                    <option value="">Semua RT</option>
                    @foreach ($rts as $rt)
                        <option value="{{ $rt->id }}" {{ request('rt_id') == $rt->id ? 'selected' : '' }}>{{ $rt->nama_rt }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="sebab" class="form-select form-select-sm">
                    <option value="">Semua Sebab</option>
                    @foreach (['sakit' => 'Sakit', 'kecelakaan' => 'Kecelakaan', 'usia_lanjut' => 'Usia Lanjut', 'wabah' => 'Wabah', 'lainnya' => 'Lainnya'] as $v => $l)
                        <option value="{{ $v }}" {{ request('sebab') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="tahun" class="form-control form-control-sm"
                       placeholder="Tahun" value="{{ request('tahun') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('kematian.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
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
                    <th>Almarhum/ah</th>
                    <th>JK</th>
                    <th>Umur</th>
                    <th>Tgl Meninggal</th>
                    <th>Sebab</th>
                    <th>RT</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kematians as $i => $k)
                <tr>
                    <td>{{ $kematians->firstItem() + $i }}</td>
                    <td><code>{{ $k->kode_kematian }}</code></td>
                    <td><strong>{{ $k->warga->nama ?? '-' }}</strong><br><small class="text-muted">{{ $k->warga->nik ?? '' }}</small></td>
                    <td>{{ $k->warga->jenis_kelamin ?? '-' }}</td>
                    <td>{{ $k->warga->umur ?? '-' }} th</td>
                    <td><small>{{ $k->tanggal_meninggal->format('d M Y') }}</small></td>
                    <td><span class="badge bg-secondary">{{ $k->sebab_label }}</span></td>
                    <td>{{ $k->rt->nama_rt ?? '-' }}</td>
                    <td class="text-center">
                        <a href="{{ route('kematian.show', $k) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada data kematian.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($kematians->hasPages())
    <div class="card-footer bg-white">{{ $kematians->links() }}</div>
    @endif
</div>

@endsection