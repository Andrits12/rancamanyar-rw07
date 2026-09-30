@extends('layouts.admin')

@section('title', 'Lowongan Kerja')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Lowongan Kerja</h5>
        <small class="text-muted">Info lowongan kerja untuk warga RW 07</small>
    </div>
    @can('lowongan.create')
    <a href="{{ route('lowongan.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Buat Lowongan
    </a>
    @endcan
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('lowongan.index') }}" class="row g-2">
            <div class="col-md-5">
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Cari posisi / perusahaan..." value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <select name="jenis" class="form-select form-select-sm">
                    <option value="">Semua Jenis</option>
                    @foreach (['full_time' => 'Full Time', 'part_time' => 'Part Time', 'kontrak' => 'Kontrak', 'magang' => 'Magang', 'freelance' => 'Freelance'] as $v => $l)
                        <option value="{{ $v }}" {{ request('jenis') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach (['aktif' => 'Aktif', 'ditutup' => 'Ditutup', 'draft' => 'Draft'] as $v => $l)
                        <option value="{{ $v }}" {{ request('status') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('lowongan.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Daftar Lowongan --}}
<div class="row g-3">
    @forelse ($lowongans as $l)
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            @if ($l->is_pinned)
                <div class="position-absolute" style="top: 10px; right: 10px;">
                    <span class="badge bg-warning text-dark"><i class="bi bi-pin-angle-fill"></i> Pinned</span>
                </div>
            @endif

            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-1">
                            <a href="{{ route('lowongan.show', $l) }}" class="text-decoration-none">
                                {{ $l->judul }}
                            </a>
                        </h6>
                        <div class="text-muted small">
                            <i class="bi bi-building"></i> {{ $l->perusahaan }}
                        </div>
                    </div>
                    <span class="badge {{ $l->status_badge }}">{{ ucfirst($l->status) }}</span>
                </div>

                <div class="mb-2">
                    <span class="badge bg-info text-dark">{{ $l->jenis_label }}</span>
                    @if ($l->lokasi)
                        <span class="badge bg-secondary"><i class="bi bi-geo-alt"></i> {{ $l->lokasi }}</span>
                    @endif
                </div>

                <p class="small text-muted mb-2">{{ Str::limit($l->deskripsi, 100) }}</p>

                <div class="row small text-muted">
                    <div class="col-6">
                        <i class="bi bi-cash"></i> {{ $l->gaji_range }}
                    </div>
                    <div class="col-6 text-end">
                        @if ($l->deadline)
                            @if ($l->sisa_hari > 0)
                                <i class="bi bi-clock"></i> {{ $l->sisa_hari }} hari lagi
                            @else
                                <span class="text-danger"><i class="bi bi-x-circle"></i> Ditutup</span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="bi bi-people"></i> {{ $l->lamarans_count }} pelamar
                </small>
                <a href="{{ route('lowongan.show', $l) }}" class="btn btn-sm btn-primary">
                    Lihat Detail <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-4 text-muted">
                <i class="bi bi-briefcase" style="font-size: 40px;"></i>
                <p class="mb-0 mt-2">Belum ada lowongan kerja.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if ($lowongans->hasPages())
<div class="mt-3">{{ $lowongans->links() }}</div>
@endif

@endsection