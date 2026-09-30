<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $tipe = $request->get('tipe', 'pengumuman');

        $query = Pengumuman::with('penulis')->where('tipe', $tipe);

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $query->where('judul', 'like', '%' . $request->q . '%');
        }

        $pengumuman = $query->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('pengumuman.index', compact('pengumuman', 'tipe'));
    }

    public function create(Request $request)
    {
        $tipe = $request->get('tipe', 'pengumuman');
        return view('pengumuman.create', compact('tipe'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipe' => 'required|in:pengumuman,berita',
            'judul' => 'required|string|max:200',
            'kategori' => 'required|string|max:50',
            'ringkasan' => 'nullable|string|max:500',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|max:2048', // max 2MB
            'status' => 'required|in:draft,published,arsip',
            'is_pinned' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        // Handle upload gambar
        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('pengumuman', 'public');
        }

        $validated['penulis_id'] = auth()->id();
        $validated['is_pinned'] = $request->boolean('is_pinned');

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        Pengumuman::create($validated);

        return redirect()->route('pengumuman.index', ['tipe' => $validated['tipe']])
            ->with('success', 'Data berhasil disimpan.');
    }

    public function show(Pengumuman $pengumuman)
    {
        $pengumuman->increment('views');
        return view('pengumuman.show', compact('pengumuman'));
    }

    public function edit(Pengumuman $pengumuman)
    {
        return view('pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'kategori' => 'required|string|max:50',
            'ringkasan' => 'nullable|string|max:500',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,arsip',
            'is_pinned' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        // Handle upload gambar baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama
            if ($pengumuman->gambar && Storage::disk('public')->exists($pengumuman->gambar)) {
                Storage::disk('public')->delete($pengumuman->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('pengumuman', 'public');
        }

        $validated['is_pinned'] = $request->boolean('is_pinned');

        if ($validated['status'] === 'published' && !$pengumuman->published_at) {
            $validated['published_at'] = now();
        }

        $pengumuman->update($validated);

        return redirect()->route('pengumuman.index', ['tipe' => $pengumuman->tipe])
            ->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        // Hapus gambar
        if ($pengumuman->gambar && Storage::disk('public')->exists($pengumuman->gambar)) {
            Storage::disk('public')->delete($pengumuman->gambar);
        }

        $tipe = $pengumuman->tipe;
        $pengumuman->delete();

        return redirect()->route('pengumuman.index', ['tipe' => $tipe])
            ->with('success', 'Data berhasil dihapus.');
    }
}