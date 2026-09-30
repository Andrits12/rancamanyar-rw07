<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Gagal - RW Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .verify-card { max-width: 500px; background: #fff; border-radius: 15px; padding: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); text-align: center; }
        .icon-invalid { width: 80px; height: 80px; background: #dc2626; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 20px; }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="icon-invalid">
            <i class="bi bi-x-circle-fill"></i>
        </div>
        <h3 class="mb-2">Verifikasi Gagal ❌</h3>
        <p class="text-muted">
            QR Code tidak valid atau surat tidak ditemukan dalam sistem.
            Pastikan Anda memindai QR Code yang benar.
        </p>
        <a href="/" class="btn btn-primary mt-3">
            <i class="bi bi-house"></i> Kembali ke Beranda
        </a>
    </div>
</body>
</html>