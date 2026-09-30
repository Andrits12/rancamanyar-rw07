<?php

namespace App\Http\Controllers;

use App\Models\JenisSurat;
use Illuminate\Http\Request;

class JenisSuratController extends Controller
{
    public function index()
    {
        $jenisSurats = JenisSurat::orderBy('urutan')->paginate(15);
        return view('jenis-surat.index', compact('jenisSurats'));
    }

    public function create()
    {
        return view('jenis-surat.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:jenis_surat,kode',
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'syarat' => 'nullable|string',
            'urutan' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        JenisSurat::create($validated);

        return redirect()->route('jenis-surat.index')->with('success', 'Jenis surat ditambahkan.');
    }

    public function show(JenisSurat $jenisSurat)
    {
        return view('jenis-surat.show', compact('jenisSurat'));
    }

    public function edit(JenisSurat $jenisSurat)
    {
        return view('jenis-surat.edit', compact('jenisSurat'));
    }

    public function update(Request $request, JenisSurat $jenisSurat)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:jenis_surat,kode,' . $jenisSurat->id,
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'syarat' => 'nullable|string',
            'urutan' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $jenisSurat->update($validated);

        return redirect()->route('jenis-surat.index')->with('success', 'Jenis surat diperbarui.');
    }

    public function destroy(JenisSurat $jenisSurat)
    {
        if ($jenisSurat->surats()->count() > 0) {
            return back()->with('error', 'Jenis surat masih dipakai.');
        }
        $jenisSurat->delete();
        return redirect()->route('jenis-surat.index')->with('success', 'Jenis surat dihapus.');
    }
}