@extends('layouts.admin')

@section('title', 'Inventaris RW')

@section('content')

@if (auth()->user()->hasRole('warga'))
<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    Ingin meminjam aset? Silakan lapor ke <strong>Ketua RT</strong> Anda terlebih dahulu.
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Inventaris RW</h5>
        <small class="text-muted">Daftar aset & perlengkapan RW</small>
    </div>
    @can('inventaris.create')
    <a href="{{ route('inventaris.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Aset
    </a>
    @endcan
</div>

{{-- Statistik --}}
<div class="row g-2 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Total Jenis Aset</small>
                <h5 class="mb-0">{{ $stats['total'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Total Unit</small>
                <h5 class="mb-0">{{ $stats['total_unit'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Aset Rusak</small>
                <h5 class="mb-0 text-danger">{{ $stats['rusak'] }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('inventaris.index') }}" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Cari nama / kode aset..." value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <select name="kategori" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach (['tenda' => 'Tenda', 'kursi' => 'Kursi', 'meja' => 'Meja', 'elektronik' => 'Elektronik', 'alat_kerja' => 'Alat Kerja', 'perlengkapan' => 'Perlengkapan', 'lainnya' => 'Lainnya'] as $v => $l)
                        <option value="{{ $v }}" {{ request('kategori') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="kondisi" class="form-select form-select-sm">
                    <option value="">Semua Kondisi</option>
                    @foreach (['baik' => 'Baik', 'rusak_ringan' => 'Rusak Ringan', 'rusak_berat' => 'Rusak Berat', 'hilang' => 'Hilang'] as $v => $l)
                        <option value="{{ $v }}" {{ request('kondisi') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('inventaris.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
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
                    <th width="80">Foto</th>
                    <th>Kode</th>
                    <th>Nama Aset</th>
                    <th>Kategori</th>
                    <th>Pemilik</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Dipinjam</th>
                    <th class="text-center">Tersedia</th>
                    <th>Kondisi</th>
                    <th width="150" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($asets as $i => $a)
                <tr>
                    <td>{{ $asets->firstItem() + $i }}</td>
                    <td>
                        @if ($a->foto_url)
                            <img src="{{ $a->foto_url }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                        @else
                            <div class="bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center"
                                 style="width: 50px; height: 50px; border-radius: 5px;">
                                <i class="bi bi-box-seam text-secondary"></i>
                            </div>
                        @endif
                    </td>
                    <td><code>{{ $a->kode_aset }}</code></td>
                    <td>
                        <a href="{{ route('inventaris.show', $a) }}" class="text-decoration-none">
                            <strong>{{ $a->nama }}</strong>
                        </a>
                        @if ($a->lokasi_penyimpanan)
                            <br><small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $a->lokasi_penyimpanan }}</small>
                        @endif
                    </td>
                    <td><span class="badge bg-info text-dark">{{ $a->kategori_label }}</span></td>
                    <div class="col-md-2">
                        <select name="pemilik" class="form-select form-select-sm">
                            <option value="">Semua Pemilik</option>
                            <option value="rw" {{ request('pemilik') == 'rw' ? 'selected' : '' }}>RW</option>
                            <option value="rt" {{ request('pemilik') == 'rt' ? 'selected' : '' }}>RT</option>
                        </select>
                    </div>
                    <td class="text-center">{{ $a->jumlah_total }} {{ $a->satuan }}</td>
                    <td class="text-center">
                        @if ($a->jumlah_dipinjam > 0)
                            <span class="badge bg-warning text-dark">{{ $a->jumlah_dipinjam }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center"><strong>{{ $a->jumlah_tersedia }}</strong></td>
                    <td><span class="badge {{ $a->kondisi_badge }}">{{ ucwords(str_replace('_', ' ', $a->kondisi)) }}</span></td>
                    <td class="text-center">
                        <a href="{{ route('inventaris.show', $a) }}" class="btn btn-sm btn-outline-info" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                        @can('inventaris.pinjam')
                        <a href="{{ route('peminjaman-aset.create', ['aset_id' => $a->id]) }}" class="btn btn-sm btn-outline-primary" title="Pinjam">
                            <i class="bi bi-box-arrow-up"></i>
                        </a>
                        @endcan
                        @can('inventaris.edit')
                        <a href="{{ route('inventaris.edit', $a) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada data aset.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($asets->hasPages())
    <div class="card-footer bg-white">{{ $asets->links() }}</div>
    @endif
</div>

@endsection