@extends('layouts.admin')

@section('title', 'Detail Jadwal Ronda')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Detail Jadwal Ronda</h5>
        <small class="text-muted">{{ $ronda->tanggal->format('d F Y') }} • {{ $ronda->shift_label }}</small>
    </div>
    <div>
        @can('ronda.edit')
        <a href="{{ route('ronda.edit', $ronda) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endcan
        <a href="{{ route('ronda.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Info Jadwal</strong></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th>Tanggal</th><td>{{ $ronda->tanggal->format('d M Y') }}</td></tr>
                    <tr><th>RT</th><td>{{ $ronda->rt->nama_rt ?? '-' }}</td></tr>
                    <tr><th>Shift</th><td>{{ $ronda->shift_label }}</td></tr>
                    <tr><th>Jam</th><td>
                        {{ $ronda->jam_mulai ? \Carbon\Carbon::parse($ronda->jam_mulai)->format('H:i') : '-' }}
                        -
                        {{ $ronda->jam_selesai ? \Carbon\Carbon::parse($ronda->jam_selesai)->format('H:i') : '-' }}
                    </td></tr>
                    <tr><th>Pos</th><td>{{ $ronda->pos_ronda ?? '-' }}</td></tr>
                    <tr><th>Koordinator</th><td>{{ $ronda->koordinator_nama ?? $ronda->koordinator->name ?? '-' }}</td></tr>
                    <tr><th>Status</th><td><span class="badge {{ $ronda->status_badge }}">{{ ucfirst($ronda->status) }}</span></td></tr>
                </table>
            </div>
        </div>

        @if ($ronda->catatan)
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white"><strong>Catatan</strong></div>
            <div class="card-body"><p class="mb-0 small">{{ $ronda->catatan }}</p></div>
        </div>
        @endif
    </div>

    <div class="col-md-8">
        {{-- Daftar Anggota --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong><i class="bi bi-people"></i> Anggota Ronda ({{ $ronda->anggotas->count() }})</strong></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>No HP</th>
                            <th>Kehadiran</th>
                            <th>Waktu Absen</th>
                            @can('ronda.absen')
                            <th width="120" class="text-center">Aksi</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ronda->anggotas as $a)
                        <tr>
                            <td><strong>{{ $a->nama_manual ?? $a->user->name ?? '-' }}</strong></td>
                            <td><small>{{ $a->user->no_hp ?? '-' }}</small></td>
                            <td>
                                @if ($a->hadir)
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Hadir</span>
                                @else
                                    <span class="badge bg-secondary">Belum Absen</span>
                                @endif
                            </td>
                            <td><small>{{ $a->absen_at?->format('d M Y H:i') ?? '-' }}</small></td>
                            @can('ronda.absen')
                            <td class="text-center">
                                @if (!$a->hadir)
                                <form method="POST" action="{{ route('ronda.absen', $ronda) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="anggota_id" value="{{ $a->id }}">
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-check"></i> Absen
                                    </button>
                                </form>
                                @else
                                <small class="text-muted">✓</small>
                                @endif
                            </td>
                            @endcan
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Form Laporan --}}
        @can('ronda.lapor')
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white"><strong><i class="bi bi-file-text"></i> Buat Laporan Ronda</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('ronda.lapor', $ronda) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Kondisi</label>
                            <select name="kondisi" class="form-select form-select-sm" required>
                                <option value="aman">✅ Aman</option>
                                <option value="ada_kejadian">⚠️ Ada Kejadian</option>
                                <option value="perlu_tindak_lanjut">🔴 Perlu Tindak Lanjut</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Foto (opsional)</label>
                            <input type="file" name="foto" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control form-control-sm" rows="3" required></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm mt-2">
                        <i class="bi bi-send"></i> Kirim Laporan
                    </button>
                </form>
            </div>
        </div>
        @endcan

        {{-- Riwayat Laporan --}}
        @if ($ronda->laporans->count() > 0)
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white"><strong><i class="bi bi-clock-history"></i> Riwayat Laporan</strong></div>
            <div class="card-body">
                @foreach ($ronda->laporans as $l)
                <div class="border-bottom pb-2 mb-2">
                    <div class="d-flex justify-content-between">
                        <span class="badge {{ $l->kondisi_badge }}">{{ str_replace('_', ' ', ucfirst($l->kondisi)) }}</span>
                        <small class="text-muted">{{ $l->created_at->format('d M Y H:i') }}</small>
                    </div>
                    <div class="small mt-1">{{ $l->catatan }}</div>
                    <small class="text-muted">oleh {{ $l->user->name ?? '-' }}</small>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@endsection