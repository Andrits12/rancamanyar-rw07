<?php

namespace App\Http\Controllers;

use App\Models\LokasiPenting;
use App\Models\Rt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PetaController extends Controller
{
    /**
     * Tampilkan peta utama
     */
    public function index(Request $request)
    {
        $query = LokasiPenting::with(['rt', 'warga'])->aktif();

        if ($request->has('kategori') && $request->kategori != '') {
            $query->where('kategori', $request->kategori);
        }

        if ($request->has('rt_id') && $request->rt_id != '') {
            $query->where('rt_id', $request->rt_id);
        }

        if ($request->has('q') && $request->q != '') {
            $query->where('nama', 'like', '%' . $request->q . '%');

        }

        $lokasis = $query->orderBy('nama')->get();

        // Statistik per kategori
        $stats = LokasiPenting::aktif()
            ->selectRaw('kategori, count(*) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        // Ambil RT untuk filter
        $rts = Rt::orderBy('nomor_rt')->get();

        \Log::info('=== PETA FILTER ===', [
            'semua_request' => $request->all(),
            'kategori' => $request->kategori,
            'q' => $request->q,
            'rt_id' => $request->rt_id,
        ]);    

        return view('peta.index', compact('lokasis', 'stats', 'rts'));
    }

    /**
     * Form tambah lokasi
     */
    public function create()
    {
        $rts = Rt::orderBy('nomor_rt')->get();
        return view('peta.create', compact('rts'));
    }

    /**
     * Simpan lokasi baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'kategori' => 'required|in:rumah_warga,pos_ronda,posyandu,masjid,sekolah,umkm,aset_rw,fasilitas_umum,lainnya',
            'deskripsi' => 'nullable|string',
            'alamat' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'rt_id' => 'nullable|exists:rt,id',
            'kontak' => 'nullable|string|max:100',
            'foto' => 'nullable|image|max:2048',
            'is_public' => 'nullable|boolean',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('lokasi', 'public');
        }

        $validated['warna'] = (new LokasiPenting(['kategori' => $validated['kategori']]))->kategori_color;
        $validated['is_public'] = $request->boolean('is_public', true);
        $validated['is_active'] = true;
        $validated['dibuat_oleh'] = auth()->id();

        LokasiPenting::create($validated);

        return redirect()->route('peta.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }

    /**
     * Detail lokasi
     */
    public function show($id)
    {
        $lokasi = LokasiPenting::with(['rt', 'warga', 'pembuat'])->findOrFail($id);
        return view('peta.show', ['lokasi' => $lokasi]);
    }
    
    public function edit($id)
    {
        $lokasi = LokasiPenting::findOrFail($id);
        $rts = Rt::orderBy('nomor_rt')->get();
        return view('peta.edit', ['lokasi' => $lokasi, 'rts' => $rts]);
    }
    
    public function update(Request $request, $id)
    {
        $lokasi = LokasiPenting::findOrFail($id);
        
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'kategori' => 'required|in:rumah_warga,pos_ronda,posyandu,masjid,sekolah,umkm,aset_rw,fasilitas_umum,lainnya',
            'deskripsi' => 'nullable|string',
            'alamat' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'rt_id' => 'nullable|exists:rt,id',
            'kontak' => 'nullable|string|max:100',
            'foto' => 'nullable|image|max:2048',
            'is_public' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);
    
        if ($request->hasFile('foto')) {
            if ($lokasi->foto && Storage::disk('public')->exists($lokasi->foto)) {
                Storage::disk('public')->delete($lokasi->foto);
            }
            $validated['foto'] = $request->file('foto')->store('lokasi', 'public');
        }
    
        $validated['warna'] = (new LokasiPenting(['kategori' => $validated['kategori']]))->kategori_color;
        $validated['is_public'] = $request->boolean('is_public', true);
        $validated['is_active'] = $request->boolean('is_active', true);
    
        $lokasi->update($validated);
    
        return redirect()->route('peta.show', $lokasi->id)->with('success', 'Lokasi diperbarui.');
    }
    
    public function destroy($id)
    {
        $lokasi = LokasiPenting::findOrFail($id);
    
        if ($lokasi->foto && Storage::disk('public')->exists($lokasi->foto)) {
            Storage::disk('public')->delete($lokasi->foto);
        }
    
        $lokasi->delete();
    
        return redirect()->route('peta.index')->with('success', 'Lokasi dihapus.');
    }

    /**
     * API: Ambil semua lokasi dalam JSON (untuk peta)
     */
    public function apiLokasi(Request $request)
    {
        $query = LokasiPenting::with(['rt', 'warga'])->aktif();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('rt_id')) {
            $query->where('rt_id', $request->rt_id);
        }

        $lokasis = $query->get()->map(function ($l) {
            return [
                'id' => $l->id,
                'nama' => $l->nama,
                'kategori' => $l->kategori,
                'kategori_label' => $l->kategori_label,
                'warna' => $l->warna,
                'latitude' => $l->latitude,
                'longitude' => $l->longitude,
                'alamat' => $l->alamat,
                'deskripsi' => $l->deskripsi,
                'foto_url' => $l->foto_url,
                'rt' => $l->rt->nama_rt ?? null,
                'kontak' => $l->kontak,
                'show_url' => route('peta.show', $l->id),
            ];
        });

        return response()->json($lokasis);
    }
}