<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wajib Ganti Password - RW Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .card-box {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 460px;
            width: 100%;
        }
        .icon-circle {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto 20px;
        }
        .form-control { padding: 12px 15px; border-radius: 8px; }
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none; padding: 12px; border-radius: 8px;
            font-weight: 600; color: #fff; width: 100%;
        }
    </style>
</head>
<body>
    <div class="card-box">
        <div class="icon-circle">
            <i class="bi bi-shield-lock"></i>
        </div>

        <h4 class="text-center mb-2">Wajib Ganti Password</h4>
        <p class="text-center text-muted mb-4">
            Halo <strong>{{ auth()->user()->name }}</strong>,<br>
            Demi keamanan akun, silakan ganti password Anda terlebih dahulu.
        </p>

        @if ($errors->any())
            <div class="alert alert-danger py-2">
                <i class="bi bi-exclamation-circle"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.wajib.update') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-control" required autofocus>
                <small class="text-muted">Minimal 8 karakter, kombinasi huruf & angka</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Ulangi Password Baru</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn-submit">
                <i class="bi bi-check-circle"></i> Simpan Password Baru
            </button>
        </form>

        <hr class="my-4">
        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <button type="submit" class="btn btn-link text-muted btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</body>
</html>