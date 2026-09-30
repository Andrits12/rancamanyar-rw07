<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AsetController extends Controller
{
    public function index(Request $request)
{
    $user = auth()->user();
    $query = Aset::with('rt')->visibleFor($user);

    // Filter manual
    if ($request->filled('pemilik')) {
        $query->where('pemilik', $request->pemilik);
    }
    if ($request->filled('rt_id') && $user->hasAnyRole(['super_admin', 'ketua_rw', 'sekretaris'])) {
        $query->where('rt_id', $request->rt_id);
    }
    if ($request->filled('kategori')) {
        $query->where('kategori', $request->kategori);
    }
    if ($request->filled('kondisi')) {
        $query->where('kondisi', $request->kondisi);
    }
    if ($request->filled('q')) {
        $query->where(function ($sub) use ($request) {
            $sub->where('nama', 'like', '%' . $request->q . '%')
                ->orWhere('kode_aset', 'like', '%' . $request->q . '%');
        });
    }

    $asets = $query->orderBy('pemilik')->orderBy('nama')->paginate(15)->withQueryString();

    // Statistik
    $baseStats = Aset::visibleFor($user);
    $stats = [
        'total' => (clone $baseStats)->count(),
        'total_unit' => (clone $baseStats)->sum('jumlah_total'),
        'rusak' => (clone $baseStats)->whereIn('kondisi', ['rusak_ringan', 'rusak_berat'])->count(),
    ];

    $rts = \App\Models\Rt::orderBy('nomor_rt')->get();

    return view('inventaris.index', compact('asets', 'stats', 'rts'));
}

    public function create()
    {
        return view('inventaris.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'kategori' => 'required|in:tenda,kursi,meja,elektronik,alat_kerja,perlengkapan,lainnya',
            'deskripsi' => 'nullable|string',
            'jumlah_total' => 'required|integer|min:1',
            'satuan' => 'required|string|max:30',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'lokasi_penyimpanan' => 'nullable|string|max:150',
            'tanggal_perolehan' => 'nullable|date',
            'harga_perolehan' => 'nullable|numeric|min:0',
            'sumber_perolehan' => 'nullable|string|max:100',
            'foto' => 'nullable|image|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('aset', 'public');
        }

        $validated['is_active'] = true;

        Aset::create($validated);

        return redirect()->route('inventaris.index')->with('success', 'Aset berhasil ditambahkan.');
    }

    public function show(Aset $inventaris)
    {
        $inventaris->load(['peminjamans.user', 'peminjamans.rt']);
        return view('inventaris.show', ['aset' => $inventaris]);
    }

    public function edit(Aset $inventaris)
    {
        return view('inventaris.edit', ['aset' => $inventaris]);
    }

    public function update(Request $request, Aset $inventaris)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'kategori' => 'required|in:tenda,kursi,meja,elektronik,alat_kerja,perlengkapan,lainnya',
            'deskripsi' => 'nullable|string',
            'jumlah_total' => 'required|integer|min:1',
            'satuan' => 'required|string|max:30',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'lokasi_penyimpanan' => 'nullable|string|max:150',
            'tanggal_perolehan' => 'nullable|date',
            'harga_perolehan' => 'nullable|numeric|min:0',
            'sumber_perolehan' => 'nullable|string|max:100',
            'foto' => 'nullable|image|max:2048',
            'is_active' => 'nullable|boolean',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->hasFile('foto')) {
            if ($inventaris->foto && Storage::disk('public')->exists($inventaris->foto)) {
                Storage::disk('public')->delete($inventaris->foto);
            }
            $validated['foto'] = $request->file('foto')->store('aset', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $inventaris->update($validated);

        return redirect()->route('inventaris.index')->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy(Aset $inventaris)
    {
        if ($inventaris->peminjamanAktif()->count() > 0) {
            return back()->with('error', 'Aset sedang dipinjam. Tidak bisa dihapus.');
        }

        if ($inventaris->foto && Storage::disk('public')->exists($inventaris->foto)) {
            Storage::disk('public')->delete($inventaris->foto);
        }

        $inventaris->delete();

        return redirect()->route('inventaris.index')->with('success', 'Aset berhasil dihapus.');
    }
}