<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat - RW Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; min-height: 100vh; padding: 30px 0; }
        .verify-card { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 15px; padding: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
        .icon-verified { width: 80px; height: 80px; background: #16a34a; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 20px; }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="icon-verified">
            <i class="bi bi-patch-check-fill"></i>
        </div>

        <h3 class="text-center mb-2">Surat Terverifikasi ✅</h3>
        <p class="text-center text-muted mb-4">
            Surat ini asli dan diterbitkan oleh RW 07 Perumahan Rancamanyar
        </p>

        <table class="table table-bordered">
            <tr><th width="180">Kode Surat</th><td><code>{{ $surat->kode_surat }}</code></td></tr>
            <tr><th>Nomor Surat</th><td>{{ $surat->nomor_surat ?? '-' }}</td></tr>
            <tr><th>Jenis Surat</th><td>{{ $surat->jenisSurat->nama ?? '-' }}</td></tr>
            <tr><th>Nama Pemohon</th><td><strong>{{ $surat->user->name }}</strong></td></tr>
            <tr><th>NIK</th><td>{{ $surat->user->nik }}</td></tr>
            <tr><th>RT</th><td>{{ $surat->rt->nama_rt ?? '-' }}</td></tr>
            <tr><th>Keperluan</th><td>{{ $surat->keperluan }}</td></tr>
            <tr><th>Tanggal Surat</th><td>{{ $surat->tanggal_surat?->format('d F Y') ?? '-' }}</td></tr>
            <tr><th>Ditandatangani Oleh</th><td>{{ $surat->penyetujuRw->name ?? '-' }}</td></tr>
            <tr><th>Status</th><td><span class="badge bg-success">{{ $surat->status_label }}</span></td></tr>
        </table>

        <div class="alert alert-success mt-3">
            <i class="bi bi-check-circle"></i>
            <strong>Dokumen ini valid.</strong> Diterbitkan pada
            {{ $surat->selesai_at?->format('d F Y, H:i') ?? '-' }}.
        </div>

        <div class="text-center mt-4">
            <small class="text-muted">
                RW 07 Perumahan Rancamanyar<br>
                Desa Wancimekar, Kecamatan Kotabaru, Kabupaten Karawang
            </small>
        </div>
    </div>
</body>
</html>