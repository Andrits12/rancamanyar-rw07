@extends('layouts.admin')

@section('title', 'Detail Peminjaman: ' . $peminjaman->kode_pinjam)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Detail Peminjaman</h5>
        <small class="text-muted">Kode: <code>{{ $peminjaman->kode_pinjam }}</code></small>
    </div>
    <a href="{{ route('peminjaman-aset.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row g-3">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge {{ $peminjaman->status_badge }}">{{ $peminjaman->status_label }}</span>
                </div>
                <h5>{{ $peminjaman->aset->nama ?? '-' }}</h5>

                <table class="table table-sm mt-3">
                    <tr><th width="180">Kode Pinjam</th><td><code>{{ $peminjaman->kode_pinjam }}</code></td></tr>
                    <tr><th>Peminjam</th><td>{{ $peminjaman->user->name ?? '-' }} ({{ $peminjaman->rt->nama_rt ?? '-' }})</td></tr>
                    <tr><th>Jumlah</th><td>{{ $peminjaman->jumlah }} {{ $peminjaman->aset->satuan ?? '' }}</td></tr>
                    <tr><th>Tanggal Pinjam</th><td>{{ $peminjaman->tanggal_pinjam?->format('d F Y') }}</td></tr>
                    <tr><th>Rencana Kembali</th><td>
                        {{ $peminjaman->tanggal_rencana_kembali?->format('d F Y') }}
                        @if ($peminjaman->terlambat) <span class="badge bg-danger">Terlambat</span> @endif
                    </td></tr>
                    @if ($peminjaman->tanggal_kembali_aktual)
                    <tr><th>Kembali Aktual</th><td>{{ $peminjaman->tanggal_kembali_aktual->format('d F Y') }}</td></tr>
                    @endif
                    @if ($peminjaman->kondisi_kembali)
                    <tr><th>Kondisi Kembali</th><td>{{ ucwords(str_replace('_', ' ', $peminjaman->kondisi_kembali)) }}</td></tr>
                    @endif
                </table>

                <hr>
                <h6>Keperluan</h6>
                <p>{{ $peminjaman->keperluan }}</p>

                @if ($peminjaman->catatan_peminjam)
                    <h6>Catatan Peminjam</h6>
                    <p class="text-muted">{{ $peminjaman->catatan_peminjam }}</p>
                @endif

                @if ($peminjaman->catatan_approval)
                    <div class="alert alert-info mt-3">
                        <strong>Catatan Approval:</strong><br>
                        {{ $peminjaman->catatan_approval }}
                        @if ($peminjaman->penyetuju)
                            <br><small>oleh {{ $peminjaman->penyetuju->name }}</small>
                        @endif
                    </div>
                @endif

                @if ($peminjaman->catatan_kembali)
                    <div class="alert alert-success mt-3">
                        <strong>Catatan Pengembalian:</strong><br>
                        {{ $peminjaman->catatan_kembali }}
                    </div>
                @endif

                @if ($peminjaman->foto_kembali)
                    <img src="{{ asset('storage/' . $peminjaman->foto_kembali) }}" class="img-fluid rounded mt-2" style="max-height: 250px;">
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-5">
        {{-- Approval --}}
        @can('inventaris.approve')
        @if ($peminjaman->status === 'diajukan')
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white"><strong><i class="bi bi-check-circle"></i> Approval</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('peminjaman-aset.approve', $peminjaman) }}" class="mb-2">
                    @csrf
                    <textarea name="catatan_approval" class="form-control form-control-sm mb-2" rows="2" placeholder="Catatan (opsional)"></textarea>
                    <button type="submit" class="btn btn-success btn-sm w-100"><i class="bi bi-check"></i> Setujui</button>
                </form>
                <hr>
                <form method="POST" action="{{ route('peminjaman-aset.tolak', $peminjaman) }}">
                    @csrf
                    <textarea name="catatan_approval" class="form-control form-control-sm mb-2" rows="2" placeholder="Alasan penolakan" required></textarea>
                    <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Yakin tolak?')"><i class="bi bi-x"></i> Tolak</button>
                </form>
            </div>
        </div>
        @endif

        @if ($peminjaman->status === 'disetujui')
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white"><strong>Serah Terima</strong></div>
            <div class="card-body">
                <p class="small text-muted">Klik tombol di bawah setelah aset diserahkan ke peminjam.</p>
                <form method="POST" action="{{ route('peminjaman-aset.tandai-dipinjam', $peminjaman) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-box-arrow-up"></i> Tandai Sedang Dipinjam</button>
                </form>
            </div>
        </div>
        @endif
        @endcan

        {{-- Pengembalian --}}
        @can('inventaris.kembalikan')
        @if (in_array($peminjaman->status, ['dipinjam', 'terlambat']))
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-success text-white"><strong><i class="bi bi-box-arrow-in-down"></i> Pengembalian</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('peminjaman-aset.kembalikan', $peminjaman) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small">Kondisi Kembali</label>
                        <select name="kondisi_kembali" class="form-select form-select-sm" required>
                            <option value="baik">Baik</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Catatan</label>
                        <textarea name="catatan_kembali" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Foto Bukti</label>
                        <input type="file" name="foto_kembali" class="form-control form-control-sm" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-success btn-sm w-100"><i class="bi bi-check"></i> Konfirmasi Pengembalian</button>
                </form>
            </div>
        </div>
        @endif
        @endcan

        {{-- Info Aset --}}
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white"><strong>Aset</strong></div>
            <div class="card-body">
                <a href="{{ route('inventaris.show', $peminjaman->aset_id) }}" class="text-decoration-none">
                    <strong>{{ $peminjaman->aset->nama ?? '-' }}</strong>
                </a>
                <div class="small text-muted">{{ $peminjaman->aset->kode_aset ?? '-' }}</div>
            </div>
        </div>
    </div>
</div>

@endsection