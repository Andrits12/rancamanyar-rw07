@extends('layouts.admin')

@section('title', 'Detail Surat: ' . $surat->kode_surat)

@push('styles')
<style>
    .timeline { position: relative; padding-left: 30px; }
    .timeline::before {
        content: ''; position: absolute; left: 8px; top: 0; bottom: 0;
        width: 2px; background: #e2e8f0;
    }
    .timeline-item { position: relative; margin-bottom: 20px; }
    .timeline-item::before {
        content: ''; position: absolute; left: -30px; top: 5px;
        width: 18px; height: 18px; border-radius: 50%;
        background: #667eea; border: 3px solid #fff; box-shadow: 0 0 0 2px #e2e8f0;
    }
    .timeline-item.rejected::before { background: #dc2626; }
    .timeline-item.success::before { background: #16a34a; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Detail Surat</h5>
        <small class="text-muted">Kode: <code>{{ $surat->kode_surat }}</code></small>
    </div>
    <div>
        @if ($surat->status === 'diajukan' && $surat->user_id === auth()->id())
            @can('surat.edit')
            <a href="{{ route('surat.edit', $surat) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil"></i> Edit
            </a>
            @endcan
        @endif

        @if ($surat->status === 'selesai')
        <a href="{{ route('surat.pdf.view', $surat) }}" target="_blank" class="btn btn-info btn-sm">
            <i class="bi bi-eye"></i> Preview PDF
        </a>
        
        <a href="{{ route('surat.pdf.download', $surat) }}" class="btn btn-success btn-sm">
            <i class="bi bi-download"></i> Unduh PDF
        </a>
        @endif
        
        <a href="{{ route('surat.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    {{-- Kolom Kiri: Detail --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge {{ $surat->status_badge }}">{{ $surat->status_label }}</span>
                </div>

                <h5>{{ $surat->jenisSurat->nama ?? '-' }}</h5>

                <table class="table table-sm mt-3">
                    <tr><th width="180">Kode Surat</th><td><code>{{ $surat->kode_surat }}</code></td></tr>
                    <tr><th>Pemohon</th><td>{{ $surat->user->name ?? '-' }}</td></tr>
                    <tr><th>NIK</th><td>{{ $surat->user->nik ?? '-' }}</td></tr>
                    <tr><th>RT</th><td>{{ $surat->rt->nama_rt ?? '-' }}</td></tr>
                    <tr><th>Tanggal Pengajuan</th><td>{{ $surat->created_at->format('d F Y, H:i') }}</td></tr>
                    @if ($surat->nomor_surat)
                    <tr><th>Nomor Surat Resmi</th><td><strong>{{ $surat->nomor_surat }}</strong></td></tr>
                    @endif
                    @if ($surat->tanggal_surat)
                    <tr><th>Tanggal Surat</th><td>{{ $surat->tanggal_surat->format('d F Y') }}</td></tr>
                    @endif
                </table>

                <hr>

                <h6>Keperluan</h6>
                <p style="white-space: pre-line;">{{ $surat->keperluan }}</p>

                @if ($surat->catatan_pemohon)
                    <h6 class="mt-3">Catatan Pemohon</h6>
                    <p class="text-muted">{{ $surat->catatan_pemohon }}</p>
                @endif
            </div>
        </div>

        {{-- Timeline --}}
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white"><strong><i class="bi bi-clock-history"></i> Riwayat Proses</strong></div>
            <div class="card-body">
                <div class="timeline">
                    @foreach ($surat->logs as $log)
                        <div class="timeline-item {{ str_contains($log->aksi, 'ditolak') ? 'rejected' : ($log->aksi === 'selesai' ? 'success' : '') }}">
                            <div class="d-flex justify-content-between">
                                <strong>{{ ucwords(str_replace('_', ' ', $log->aksi)) }}</strong>
                                <small class="text-muted">{{ $log->created_at->format('d M Y, H:i') }}</small>
                            </div>
                            <small class="text-muted">oleh {{ $log->user->name ?? 'Sistem' }}</small>
                            @if ($log->catatan)
                                <div class="mt-1 small">{{ $log->catatan }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Aksi --}}
    <div class="col-md-4">

        {{-- Verifikasi RT --}}
        @if ($surat->status === 'diajukan' && auth()->user()->hasAnyRole(['ketua_rt', 'super_admin']))
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <strong><i class="bi bi-check-circle"></i> Verifikasi RT</strong>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('surat.approve-rt', $surat) }}" class="mb-2">
                    @csrf
                    <textarea name="catatan" class="form-control form-control-sm mb-2" rows="2"
                              placeholder="Catatan (opsional)"></textarea>
                    <button type="submit" class="btn btn-success btn-sm w-100">
                        <i class="bi bi-check-circle"></i> Setujui & Teruskan ke RW
                    </button>
                </form>
                <hr>
                <form method="POST" action="{{ route('surat.tolak-rt', $surat) }}">
                    @csrf
                    <textarea name="catatan" class="form-control form-control-sm mb-2" rows="2"
                              placeholder="Alasan penolakan (wajib)" required></textarea>
                    <button type="submit" class="btn btn-danger btn-sm w-100"
                            onclick="return confirm('Yakin tolak surat ini?')">
                        <i class="bi bi-x-circle"></i> Tolak
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Approval RW --}}
        @if ($surat->status === 'verifikasi_rw' && auth()->user()->hasAnyRole(['ketua_rw', 'super_admin']))
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <strong><i class="bi bi-pen"></i> Approval & Tanda Tangan RW</strong>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('surat.approve-rw', $surat) }}" class="mb-2">
                    @csrf
                    <textarea name="catatan" class="form-control form-control-sm mb-2" rows="2"
                              placeholder="Catatan (opsional)"></textarea>
                    <button type="submit" class="btn btn-success btn-sm w-100"
                            onclick="return confirm('Setujui & tanda tangani surat ini?')">
                        <i class="bi bi-check-circle"></i> Setujui & Tanda Tangan
                    </button>
                </form>
                <hr>
                <form method="POST" action="{{ route('surat.tolak-rw', $surat) }}">
                    @csrf
                    <textarea name="catatan" class="form-control form-control-sm mb-2" rows="2"
                              placeholder="Alasan penolakan (wajib)" required></textarea>
                    <button type="submit" class="btn btn-danger btn-sm w-100"
                            onclick="return confirm('Yakin tolak surat ini?')">
                        <i class="bi bi-x-circle"></i> Tolak
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Info Status --}}
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white"><strong><i class="bi bi-info-circle"></i> Status</strong></div>
            <div class="card-body">
                <div class="text-center">
                    <span class="badge {{ $surat->status_badge }}" style="font-size: 14px; padding: 8px 15px;">
                        {{ $surat->status_label }}
                    </span>
                </div>
                <hr>
                @if ($surat->verifikatorRt)
                    <small class="d-block"><strong>Verifikator RT:</strong> {{ $surat->verifikatorRt->name }}</small>
                    <small class="d-block text-muted">{{ $surat->verifikasi_rt_at?->format('d M Y, H:i') }}</small>
                @endif
                @if ($surat->penyetujuRw)
                    <small class="d-block mt-2"><strong>Penyetuju RW:</strong> {{ $surat->penyetujuRw->name }}</small>
                    <small class="d-block text-muted">{{ $surat->approval_rw_at?->format('d M Y, H:i') }}</small>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection