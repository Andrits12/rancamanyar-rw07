@extends('layouts.admin')

@section('title', $pengumuman->judul)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Detail {{ $pengumuman->tipe === 'berita' ? 'Berita' : 'Pengumuman' }}</h5>
        <small class="text-muted">{{ $pengumuman->views }} kali dilihat</small>
    </div>
    <div>
        @can('pengumuman.edit')
        <a href="{{ route('pengumuman.edit', $pengumuman) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endcan
        <a href="{{ route('pengumuman.index', ['tipe' => $pengumuman->tipe]) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm">
            @if ($pengumuman->gambar)
                <img src="{{ $pengumuman->gambar_url }}" class="card-img-top" style="max-height: 400px; object-fit: cover;">
            @endif

            <div class="card-body p-4">
                <div class="mb-3">
                    <span class="badge bg-info text-dark">{{ ucfirst($pengumuman->kategori) }}</span>
                    @if ($pengumuman->is_pinned)
                        <span class="badge bg-warning text-dark"><i class="bi bi-pin-angle-fill"></i> Disematkan</span>
                    @endif
                    @if ($pengumuman->status === 'draft')
                        <span class="badge bg-warning text-dark">Draft</span>
                    @elseif ($pengumuman->status === 'arsip')
                        <span class="badge bg-secondary">Arsip</span>
                    @endif
                </div>

                <h2 class="mb-3">{{ $pengumuman->judul }}</h2>

                <div class="d-flex justify-content-between text-muted small mb-4 pb-3 border-bottom">
                    <span>
                        <i class="bi bi-person"></i> {{ $pengumuman->penulis->name ?? '-' }}
                    </span>
                    <span>
                        <i class="bi bi-calendar"></i>
                        {{ $pengumuman->published_at?->format('d F Y, H:i') ?? $pengumuman->created_at->format('d F Y, H:i') }}
                    </span>
                </div>

                @if ($pengumuman->ringkasan)
                    <div class="alert alert-light border-start border-4 border-primary">
                        <em>{{ $pengumuman->ringkasan }}</em>
                    </div>
                @endif

                <div style="font-size: 16px; line-height: 1.8;">
                    {!! nl2br(e($pengumuman->konten)) !!}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection