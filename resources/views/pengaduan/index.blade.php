@extends('layouts.admin')

@section('title', 'Pengaduan Warga')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Pengaduan Warga</h5>
        <small class="text-muted">Total: {{ $pengaduans->total() }} pengaduan</small>
    </div>
    @can('pengaduan.create')
    <a href="{{ route('pengaduan.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Buat Pengaduan
    </a>
    @endcan
</div>

{{-- Statistik Cepat --}}
<div class="row g-2 mb-3">
    @php
        $baseQuery = \App\Models\Pengaduan::query();
        if (auth()->user()->hasRole('ketua_rt') && auth()->user()->rt_id) {
            $baseQuery->where('rt_id', auth()->user()->rt_id);
        }
    @endphp
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Baru</small>
                <h5 class="mb-0 text-primary">{{ (clone $baseQuery)->where('status', 'baru')->count() }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Diproses</small>
                <h5 class="mb-0 text-warning">{{ (clone $baseQuery)->where('status', 'diproses')->count() }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Selesai</small>
                <h5 class="mb-0 text-success">{{ (clone $baseQuery)->where('status', 'selesai')->count() }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Ditolak</small>
                <h5 class="mb-0 text-danger">{{ (clone $baseQuery)->where('status', 'ditolak')->count() }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('pengaduan.index') }}" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Cari judul / kode tiket..." value="{{ request('q') }}">
            </div>

            <div class="col-md-2">
                <select name="tingkat" class="form-select form-select-sm">
                    <option value="">Semua Tingkat</option>
                    <option value="rt" {{ request('tingkat') == 'rt' ? 'selected' : '' }}>RT</option>
                    <option value="rw" {{ request('tingkat') == 'rw' ? 'selected' : '' }}>RW</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach (['baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $v => $l)
                        <option value="{{ $v }}" {{ request('status') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="kategori" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach (['infrastruktur' => 'Infrastruktur', 'keamanan' => 'Keamanan', 'kebersihan' => 'Kebersihan', 'kesehatan' => 'Kesehatan', 'sosial' => 'Sosial', 'lainnya' => 'Lainnya'] as $v => $l)
                        <option value="{{ $v }}" {{ request('kategori') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="prioritas" class="form-select form-select-sm">
                    <option value="">Semua Prioritas</option>
                    <option value="tinggi" {{ request('prioritas') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                    <option value="sedang" {{ request('prioritas') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="rendah" {{ request('prioritas') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('pengaduan.index') }}" class="btn btn-sm btn-outline-secondary">
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
                    <th>Kode Tiket</th>
                    <th>Judul</th>
                    <th>Tingkat</th>
                    <th>Kategori</th>
                    <th>Prioritas</th>
                    <th>Pelapor</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengaduans as $i => $p)
                <tr>
                    <td>{{ $pengaduans->firstItem() + $i }}</td>
                    <td><code>{{ $p->kode_tiket }}</code></td>
                    <td>
                        <a href="{{ route('pengaduan.show', $p) }}" class="text-decoration-none">
                            <strong>{{ Str::limit($p->judul, 50) }}</strong>
                        </a>
                        <br><small class="text-muted">{{ Str::limit($p->deskripsi, 60) }}</small>
                    </td>
                    <td><span class="badge {{ $p->tingkat_badge }}">{{ $p->tingkat_label }}</span></td>
                    <td><span class="badge bg-info text-dark">{{ ucfirst($p->kategori) }}</span></td>
                    <td><span class="badge {{ $p->prioritas_badge }}">{{ ucfirst($p->prioritas) }}</span></td>
                    <td>
                        <small>{{ $p->user->name ?? '-' }}</small>
                        <br><small class="text-muted">{{ $p->rt->nama_rt ?? '-' }}</small>
                    </td>
                    <td><span class="badge {{ $p->status_badge }}">{{ ucfirst($p->status) }}</span></td>
                    <td><small>{{ $p->created_at->format('d M Y') }}</small></td>
                    <td class="text-center">
                        <a href="{{ route('pengaduan.show', $p) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada pengaduan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($pengaduans->hasPages())
    <div class="card-footer bg-white">{{ $pengaduans->links() }}</div>
    @endif
</div>

@endsection