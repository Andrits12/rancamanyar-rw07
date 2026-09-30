@extends('layouts.admin')

@section('title', 'Detail Lamaran: ' . $lamaran->kode_lamaran)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Detail Lamaran</h5>
        <small class="text-muted">Kode: <code>{{ $lamaran->kode_lamaran }}</code></small>
    </div>
    <a href="{{ route('lamaran.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge {{ $lamaran->status_badge }}">{{ $lamaran->status_label }}</span>
                    @if ($lamaran->is_direkomendasikan)
                        <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> Direkomendasikan RT</span>
                    @endif
                </div>

                <h5>{{ $lamaran->lowongan->judul ?? '-' }}</h5>
                <p class="text-muted">{{ $lamaran->lowongan->perusahaan ?? '-' }}</p>

                <hr>

                <h6>Data Pelamar</h6>
                <table class="table table-sm">
                    <tr><th width="180">Nama</th><td><strong>{{ $lamaran->user->name ?? '-' }}</strong></td></tr>
                    <tr><th>NIK</th><td>{{ $lamaran->user->nik ?? '-' }}</td></tr>
                    <tr><th>RT</th><td>{{ $lamaran->rt->nama_rt ?? '-' }}</td></tr>
                    <tr><th>No HP</th><td>{{ $lamaran->no_hp_pelamar ?? '-' }}</td></tr>
                    <tr><th>Email</th><td>{{ $lamaran->email_pelamar ?? '-' }}</td></tr>
                </table>

                @if ($lamaran->pengalaman)
                    <hr>
                    <h6>Pengalaman Kerja</h6>
                    <p style="white-space: pre-line;">{{ $lamaran->pengalaman }}</p>
                @endif

                <hr>
                <h6>Motivasi</h6>
                <p style="white-space: pre-line;">{{ $lamaran->motivasi }}</p>

                <hr>
                <h6>Dokumen</h6>
                <div class="d-flex gap-2 flex-wrap">
                    @if ($lamaran->cv)
                    <a href="{{ route('file.preview', $lamaran->cv) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-file-earmark-pdf"></i> Lihat CV
                    </a>
                    <a href="{{ $lamaran->cv_url }}" download class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-download"></i> Download CV
                    </a>
                    @endif
                    @if ($lamaran->surat_lamaran)
                        <a href="{{ route('file.preview', $lamaran->surat_lamaran) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-file-earmark-text"></i> Lihat Surat Lamaran
                        </a>
                    @endif
                    @if ($lamaran->portfolio)
                        <a href="{{ route('file.preview', $lamaran->portfolio) }}" target="_blank" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-briefcase"></i> Lihat Portfolio
                        </a>
                    @endif
                </div>

                @if ($lamaran->catatan_verifikasi)
                    <hr>
                    <div class="alert alert-info">
                        <strong>Catatan Verifikasi:</strong><br>
                        {{ $lamaran->catatan_verifikasi }}
                        @if ($lamaran->verifikator)
                            <br><small>oleh {{ $lamaran->verifikator->name }} — {{ $lamaran->verifikasi_at?->format('d M Y H:i') }}</small>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        {{-- Panel Verifikasi RT --}}
        @can('lowongan.verifikasi')
        @if (in_array($lamaran->status, ['diajukan', 'diverifikasi_rt']))
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white"><strong><i class="bi bi-check-circle"></i> Verifikasi RT</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('lamaran.verifikasi', $lamaran) }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small">Catatan Verifikasi</label>
                        <textarea name="catatan_verifikasi" class="form-control form-control-sm" rows="3"
                                  placeholder="Catatan tentang pelamar...">{{ $lamaran->catatan_verifikasi }}</textarea>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="is_direkomendasikan" value="1" class="form-check-input"
                               id="rek" {{ $lamaran->is_direkomendasikan ? 'checked' : '' }}>
                        <label for="rek" class="form-check-label small">
                            <i class="bi bi-star-fill text-warning"></i> Rekomendasikan pelamar ini
                        </label>
                    </div>
                    <button type="submit" class="btn btn-info btn-sm w-100">
                        <i class="bi bi-check-circle"></i> Verifikasi
                    </button>
                </form>
            </div>
        </div>
        @endif
        @endcan

        {{-- Tombol Teruskan --}}
        @can('lowongan.teruskan')
        @if (in_array($lamaran->status, ['diajukan', 'diverifikasi_rt']))
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-primary text-white"><strong><i class="bi bi-arrow-right-circle"></i> Teruskan ke PT</strong></div>
            <div class="card-body">
                <p class="small text-muted mb-2">Teruskan lamaran ini ke perusahaan untuk proses interview.</p>
                <form method="POST" action="{{ route('lamaran.teruskan', $lamaran) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm w-100"
                            onclick="return confirm('Teruskan lamaran ini ke perusahaan?')">
                        <i class="bi bi-send"></i> Teruskan ke Perusahaan
                    </button>
                </form>
            </div>
        </div>
        @endif
        @endcan

        {{-- Update Status --}}
        @can('lowongan.verifikasi')
        @if (!in_array($lamaran->status, ['diajukan']))
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-warning text-dark"><strong><i class="bi bi-arrow-repeat"></i> Update Status</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('lamaran.update-status', $lamaran) }}">
                    @csrf
                    <div class="mb-2">
                        <select name="status" class="form-select form-select-sm">
                            @foreach (['diverifikasi_rt' => 'Diverifikasi RT', 'diteruskan' => 'Diteruskan', 'interview' => 'Interview', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'] as $v => $l)
                                <option value="{{ $v }}" {{ $lamaran->status == $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <textarea name="catatan_verifikasi" class="form-control form-control-sm" rows="2"
                                  placeholder="Catatan">{{ $lamaran->catatan_verifikasi }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-warning btn-sm w-100">
                        <i class="bi bi-check"></i> Update Status
                    </button>
                </form>
            </div>
        </div>
        @endif
        @endcan
    </div>
</div>

@endsection