@extends('layouts.admin')

@section('title', 'Ganti Password')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <strong><i class="bi bi-key"></i> Ganti Password</strong>
            </div>
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger py-2">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.ganti.update') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Password Lama</label>
                        <input type="password" name="password_lama" class="form-control" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control" required>
                        <small class="text-muted">Minimal 8 karakter, kombinasi huruf & angka</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ulangi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Simpan Password
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection