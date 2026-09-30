<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use App\Models\Lamaran;
use Illuminate\Http\Request;

class LowonganController extends Controller
{
    public function index(Request $request)
    {
        $query = Lowongan::with('pembuat')->withCount('lamarans');

        // Warga hanya lihat yang aktif
        if (auth()->user()->hasRole('warga') && !auth()->user()->hasAnyRole(['super_admin', 'ketua_rw', 'ketua_rt', 'sekretaris'])) {
            $query->aktif();
        }

        // Filter
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('jenis')) $query->where('jenis', $request->jenis);
        if ($request->filled('q')) {
            $query->where(function ($sub) use ($request) {
                $sub->where('judul', 'like', '%' . $request->q . '%')
                    ->orWhere('perusahaan', 'like', '%' . $request->q . '%');
            });
        }

        $lowongans = $query->orderBy('is_pinned', 'desc')
            ->orderBy('tanggal_buka', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('lowongan.index', compact('lowongans'));
    }

    public function create()
    {
        return view('lowongan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'perusahaan' => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'kualifikasi' => 'nullable|string',
            'tanggung_jawab' => 'nullable|string',
            'jenis' => 'required|in:full_time,part_time,kontrak,magang,freelance',
            'lokasi' => 'nullable|string|max:150',
            'gaji_min' => 'nullable|string|max:50',
            'gaji_max' => 'nullable|string|max:50',
            'kontak_nama' => 'nullable|string|max:100',
            'kontak_hp' => 'nullable|string|max:20',
            'kontak_email' => 'nullable|email|max:100',
            'tanggal_buka' => 'required|date',
            'deadline' => 'nullable|date|after_or_equal:tanggal_buka',
            'status' => 'required|in:draft,aktif,ditutup',
            'is_pinned' => 'nullable|boolean',
            'keterangan' => 'nullable|string',
        ]);

        $validated['dibuat_oleh'] = auth()->id();
        $validated['is_pinned'] = $request->boolean('is_pinned');

        Lowongan::create($validated);

        return redirect()->route('lowongan.index')->with('success', 'Lowongan berhasil dibuat.');
    }

    public function show(Lowongan $lowongan)
    {
        $lowongan->load(['pembuat', 'lamarans.user', 'lamarans.rt']);
        $lowongan->increment('views');

        // Cek apakah user sudah pernah lamar
        $sudahLamar = false;
        if (auth()->check() && auth()->user()->hasRole('warga')) {
            $sudahLamar = Lamaran::where('lowongan_id', $lowongan->id)
                ->where('user_id', auth()->id())
                ->exists();
        }

        return view('lowongan.show', compact('lowongan', 'sudahLamar'));
    }

    public function edit(Lowongan $lowongan)
    {
        return view('lowongan.edit', compact('lowongan'));
    }

    public function update(Request $request, Lowongan $lowongan)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'perusahaan' => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'kualifikasi' => 'nullable|string',
            'tanggung_jawab' => 'nullable|string',
            'jenis' => 'required|in:full_time,part_time,kontrak,magang,freelance',
            'lokasi' => 'nullable|string|max:150',
            'gaji_min' => 'nullable|string|max:50',
            'gaji_max' => 'nullable|string|max:50',
            'kontak_nama' => 'nullable|string|max:100',
            'kontak_hp' => 'nullable|string|max:20',
            'kontak_email' => 'nullable|email|max:100',
            'tanggal_buka' => 'required|date',
            'deadline' => 'nullable|date|after_or_equal:tanggal_buka',
            'status' => 'required|in:draft,aktif,ditutup',
            'is_pinned' => 'nullable|boolean',
            'keterangan' => 'nullable|string',
        ]);

        $validated['is_pinned'] = $request->boolean('is_pinned');
        $lowongan->update($validated);

        return redirect()->route('lowongan.show', $lowongan)->with('success', 'Lowongan diperbarui.');
    }

    public function destroy(Lowongan $lowongan)
    {
        $lowongan->delete();
        return redirect()->route('lowongan.index')->with('success', 'Lowongan dihapus.');
    }
}