<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RW Digital Rancamanyar</title>
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
        .login-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 420px;
            width: 100%;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 25px;
        }
        .login-logo i {
            font-size: 60px;
            color: #667eea;
        }
        .login-logo h4 {
            color: #333;
            margin-top: 10px;
            font-weight: 700;
        }
        .login-logo p {
            color: #888;
            font-size: 14px;
            margin: 0;
        }
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            color: #fff;
            width: 100%;
        }
        .btn-login:hover {
            opacity: 0.9;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <i class="bi bi-house-heart-fill"></i>
            <h4>RW Digital</h4>
            <p>Perumahan Rancamanyar RW 07<br>Desa Wancimekar</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2">
                <i class="bi bi-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">No HP</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-phone"></i></span>
                    <input type="text" name="no_hp" class="form-control"
                           placeholder="081234567890"
                           value="{{ old('no_hp') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control"
                           placeholder="••••••••" required>
                </div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="bi bi-box-arrow-in-right"></i> Masuk
            </button>

            <div class="text-center mt-3">
            <a href="#" class="text-decoration-none small text-muted">
                Lupa password? (Hubungi Admin)
            </a>
            </div>
        </form>

        <hr class="my-4">
        <p class="text-center text-muted small mb-0">
            Belum punya akun? Hubungi Ketua RT / Admin RW.
        </p>
    </div>
</body>
</html>