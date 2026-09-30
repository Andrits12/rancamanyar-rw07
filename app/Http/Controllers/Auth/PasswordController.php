<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Tampilkan halaman wajib ganti password (login pertama)
     */
    public function showWajibForm()
    {
        // Kalau tidak wajib ganti, redirect ke dashboard
        if (!auth()->user()->must_change_password) {
            return redirect()->route('dashboard');
        }

        return view('auth.password-wajib');
    }

    /**
     * Simpan password baru dari halaman wajib ganti
     */
    public function updateWajib(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $user = auth()->user();

        // Cegah pakai password yang sama
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password baru tidak boleh sama dengan password lama.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
            'last_password_change' => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Password berhasil diubah. Selamat menggunakan aplikasi!');
    }

    /**
     * Tampilkan halaman ganti password (dari menu profil)
     */
    public function showGantiForm()
    {
        return view('auth.password-ganti');
    }

    /**
     * Simpan password baru dari halaman ganti password (butuh password lama)
     */
    public function updateGanti(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->withErrors(['password_lama' => 'Password lama salah.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'last_password_change' => now(),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}