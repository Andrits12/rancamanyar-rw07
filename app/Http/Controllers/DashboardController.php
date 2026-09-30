<?php

namespace App\Http\Controllers;

use App\Models\Keluarga;
use App\Models\Warga;
use App\Models\Rt;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isKetuaRt = $user->hasRole('ketua_rt') && $user->rt_id;

        // Base query — kalau Ketua RT, filter otomatis
        $wargaQuery = Warga::where('status_hidup', 'hidup');
        $keluargaQuery = Keluarga::where('status_keluarga', 'aktif');

        if ($isKetuaRt) {
            $wargaQuery->where('rt_id', $user->rt_id);
            $keluargaQuery->where('rt_id', $user->rt_id);
        }

        // ============ STATISTIK UTAMA ============
        $statistik = [
            'total_keluarga' => $keluargaQuery->count(),
            'total_warga' => $wargaQuery->count(),
            'total_laki' => (clone $wargaQuery)->where('jenis_kelamin', 'L')->count(),
            'total_perempuan' => (clone $wargaQuery)->where('jenis_kelamin', 'P')->count(),
        ];

        // ============ KATEGORI UMUR ============
        $wargas = (clone $wargaQuery)->get();
        $kategoriUmur = [
            'Balita (<1)' => 0,
            'Anak (1-5)' => 0,
            'Anak Sekolah (6-12)' => 0,
            'Remaja (13-17)' => 0,
            'Dewasa (18-59)' => 0,
            'Lansia (60+)' => 0,
        ];

        foreach ($wargas as $w) {
            $umur = $w->umur;
            if ($umur === null) continue;
            if ($umur < 1) $kategoriUmur['Balita (<1)']++;
            elseif ($umur < 6) $kategoriUmur['Anak (1-5)']++;
            elseif ($umur < 13) $kategoriUmur['Anak Sekolah (6-12)']++;
            elseif ($umur < 18) $kategoriUmur['Remaja (13-17)']++;
            elseif ($umur < 60) $kategoriUmur['Dewasa (18-59)']++;
            else $kategoriUmur['Lansia (60+)']++;
        }

        // ============ WARGA PER RT ============
        $wargaPerRt = Rt::query()
            ->when($isKetuaRt, fn($q) => $q->where('id', $user->rt_id))
            ->orderBy('nomor_rt')
            ->get()
            ->map(function ($rt) {
                return [
                    'nama' => $rt->nama_rt,
                    'jumlah' => Warga::where('rt_id', $rt->id)
                        ->where('status_hidup', 'hidup')
                        ->count(),
                ];
            });

        // ============ WARGA TERBARU ============
        $wargaTerbaru = (clone $wargaQuery)
            ->with('rt')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ============ STATISTIK AKUN ============
        $totalUser = User::when($isKetuaRt, fn($q) => $q->where('rt_id', $user->rt_id))
            ->where('status_akun', 'aktif')
            ->count();

        return view('dashboard', compact(
            'statistik',
            'kategoriUmur',
            'wargaPerRt',
            'wargaTerbaru',
            'totalUser',
            'isKetuaRt'
        ));
    }
}