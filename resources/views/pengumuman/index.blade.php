@extends('layouts.admin')

@section('title', $tipe === 'berita' ? 'Berita RW' : 'Pengumuman')

@section('content')

@php
    $isBerita = $tipe === 'berita';
    $routePrefix = 'pengumuman';
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">{{ $isBerita ? 'Berita RW' : 'Pengumuman' }}</h5>
        <small class="text-muted">Total: {{ $pengumuman->total() }} {{ $isBerita ? 'berita' : 'pengumuman' }}</small>
    </div>
    @can($isBerita ? 'berita.create' : 'pengumuman.create')
    <a href="{{ route('pengumuman.create', ['tipe' => $tipe]) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Buat {{ $isBerita ? 'Berita' : 'Pengumuman' }}
    </a>
    @endcan
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('pengumuman.index') }}" class="row g-2">
            <input type="hidden" name="tipe" value="{{ $tipe }}">

            <div class="col-md-5">
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Cari judul..." value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <select name="kategori" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach (['umum' => 'Umum', 'keamanan' => 'Keamanan', 'kegiatan' => 'Kegiatan', 'kesehatan' => 'Kesehatan', 'lainnya' => 'Lainnya'] as $v => $l)
                        <option value="{{ $v }}" {{ request('kategori') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="arsip" {{ request('status') == 'arsip' ? 'selected' : '' }}>Arsip</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('pengumuman.index', ['tipe' => $tipe]) }}" class="btn btn-sm btn-outline-secondary">
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
                    <th width="80">Gambar</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Penulis</th>
                    <th>Tgl Publish</th>
                    <th width="150" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengumuman as $i => $p)
                <tr>
                    <td>{{ $pengumuman->firstItem() + $i }}</td>
                    <td>
                        @if ($p->gambar)
                            <img src="{{ $p->gambar_url }}" alt="Gambar"
                                 style="width: 60px; height: 45px; object-fit: cover; border-radius: 5px;">
                        @else
                            <div class="bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center"
                                 style="width: 60px; height: 45px; border-radius: 5px;">
                                <i class="bi bi-image text-secondary"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        @if ($p->is_pinned)
                            <i class="bi bi-pin-angle-fill text-warning" title="Disematkan"></i>
                        @endif
                        <strong>{{ Str::limit($p->judul, 60) }}</strong>
                        @if ($p->ringkasan)
                            <br><small class="text-muted">{{ Str::limit($p->ringkasan, 70) }}</small>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-info text-dark">{{ ucfirst($p->kategori) }}</span>
                    </td>
                    <td>
                        @if ($p->status === 'published')
                            <span class="badge bg-success">Published</span>
                        @elseif ($p->status === 'draft')
                            <span class="badge bg-warning text-dark">Draft</span>
                        @else
                            <span class="badge bg-secondary">Arsip</span>
                        @endif
                    </td>
                    <td><small>{{ $p->penulis->name ?? '-' }}</small></td>
                    <td>
                        <small>{{ $p->published_at?->format('d M Y') ?? '-' }}</small>
                        <br><small class="text-muted">{{ $p->views }} views</small>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('pengumuman.show', $p) }}" class="btn btn-sm btn-outline-info" title="Lihat">
                            <i class="bi bi-eye"></i>
                        </a>
                        @can($isBerita ? 'berita.edit' : 'pengumuman.edit')
                        <a href="{{ route('pengumuman.edit', $p) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endcan
                        @can($isBerita ? 'berita.delete' : 'pengumuman.delete')
                        <form method="POST" action="{{ route('pengumuman.destroy', $p) }}" class="d-inline"
                              onsubmit="return confirm('Yakin hapus {{ $p->judul }}?')">
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
                        <p class="mb-0 mt-2">Belum ada {{ $isBerita ? 'berita' : 'pengumuman' }}.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($pengumuman->hasPages())
    <div class="card-footer bg-white">{{ $pengumuman->links() }}</div>
    @endif
</div>

@endsection