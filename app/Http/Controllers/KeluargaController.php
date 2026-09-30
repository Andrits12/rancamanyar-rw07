<?php

namespace App\Http\Controllers;

use App\Models\Keluarga;
use App\Models\Rt;
use App\Models\Warga;
use Illuminate\Http\Request;

class KeluargaController extends Controller
{
    /**
     * Daftar keluarga (dengan filter)
     */
    public function index(Request $request)
    {
        $query = Keluarga::with(['rt', 'wargas'])->withCount('wargas');

        // Filter otomatis: Kalau Ketua RT, hanya lihat RT-nya
        if (auth()->user()->hasRole('ketua_rt') && auth()->user()->rt_id) {
            $query->where('rt_id', auth()->user()->rt_id);
        }

        // Filter manual by RT
        if ($request->filled('rt_id')) {
            $query->where('rt_id', $request->rt_id);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status_keluarga', $request->status);
        }

        // Search
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_kk', 'like', "%{$q}%")
                    ->orWhere('kepala_keluarga_nama', 'like', "%{$q}%")
                    ->orWhere('alamat', 'like', "%{$q}%");
            });
        }

        $keluargas = $query->orderBy('kepala_keluarga_nama')->paginate(15)->withQueryString();
        $rts = Rt::orderBy('nomor_rt')->get();

        return view('keluarga.index', compact('keluargas', 'rts'));
    }

    /**
     * Form buat keluarga baru
     */
    public function create()
    {
        $rts = Rt::orderBy('nomor_rt')->get();
        return view('keluarga.create', compact('rts'));
    }

    /**
     * Simpan keluarga baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_kk' => 'required|string|size:16|unique:keluarga,no_kk',
            'rt_id' => 'required|exists:rt,id',
            'kepala_keluarga_nama' => 'required|string|max:100',
            'alamat' => 'required|string',
            'status_rumah' => 'nullable|string|max:30',
            'status_keluarga' => 'required|in:aktif,pindah,nonaktif',
            'tgl_daftar' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        $validated['tgl_daftar'] = $validated['tgl_daftar'] ?? now();

        $keluarga = Keluarga::create($validated);

        return redirect()->route('keluarga.show', $keluarga)
            ->with('success', "Keluarga {$keluarga->kepala_keluarga_nama} berhasil ditambahkan. Silakan tambahkan anggota keluarga.");
    }

    /**
     * Detail keluarga + daftar anggotanya
     */
    public function show(Keluarga $keluarga)
    {
        $keluarga->load(['rt', 'wargas' => function ($q) {
            $q->orderByRaw("FIELD(status_keluarga, 'kepala_keluarga', 'istri', 'anak', 'menantu', 'cucu', 'orang_tua', 'mertua', 'famili_lain', 'lainnya')");
        }]);

        return view('keluarga.show', compact('keluarga'));
    }

    /**
     * Form edit keluarga
     */
    public function edit(Keluarga $keluarga)
    {
        $rts = Rt::orderBy('nomor_rt')->get();
        return view('keluarga.edit', compact('keluarga', 'rts'));
    }

    /**
     * Update keluarga
     */
    public function update(Request $request, Keluarga $keluarga)
    {
        $validated = $request->validate([
            'no_kk' => 'required|string|size:16|unique:keluarga,no_kk,' . $keluarga->id,
            'rt_id' => 'required|exists:rt,id',
            'kepala_keluarga_nama' => 'required|string|max:100',
            'alamat' => 'required|string',
            'status_rumah' => 'nullable|string|max:30',
            'status_keluarga' => 'required|in:aktif,pindah,nonaktif',
            'tgl_daftar' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        $keluarga->update($validated);

        return redirect()->route('keluarga.index')
            ->with('success', "Data keluarga {$keluarga->kepala_keluarga_nama} berhasil diperbarui.");
    }

    /**
     * Hapus keluarga
     */
    public function destroy(Keluarga $keluarga)
    {
        if ($keluarga->wargas()->count() > 0) {
            return back()->with('error', 'Keluarga masih memiliki anggota. Hapus anggotanya terlebih dahulu.');
        }

        $nama = $keluarga->kepala_keluarga_nama;
        $keluarga->delete();

        return redirect()->route('keluarga.index')
            ->with('success', "Keluarga {$nama} berhasil dihapus.");
    }
}