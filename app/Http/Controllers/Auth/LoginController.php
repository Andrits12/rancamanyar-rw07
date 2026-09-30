<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|string',
            'password' => 'required|string',
        ]);

        // Bersihkan no_hp (hilangkan spasi, tanda hubung, dll)
        $noHp = preg_replace('/[^0-9]/', '', $request->no_hp);

        // Coba login pakai no_hp
        if (Auth::attempt(['no_hp' => $noHp, 'password' => $request->password], $request->boolean('remember'))) {
            $user = Auth::user();

            // Cek status akun
            if ($user->status_akun !== 'aktif') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'no_hp' => 'Akun Anda belum aktif. Hubungi admin RW.',
                ]);
            }

            // Update last login
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            $request->session()->regenerate();

            // Kalau wajib ganti password, redirect ke halaman ganti password
            if ($user->must_change_password) {
                return redirect()->route('password.wajib');
            }

            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'no_hp' => 'No HP atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}