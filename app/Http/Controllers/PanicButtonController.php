<?php

namespace App\Http\Controllers;

use App\Models\PanicButton;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PanicButtonController extends Controller
{
    public function index(Request $request)
    {
        $query = PanicButton::with(['user', 'rt', 'penangan']);

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('jenis')) $query->where('jenis', $request->jenis);
        if ($request->filled('rt_id')) $query->where('rt_id', $request->rt_id);

        $panicButtons = $query->orderByRaw("FIELD(status, 'baru', 'ditangani', 'selesai', 'false_alarm')")
            ->orderBy('created_at', 'desc')
            ->paginate(20)->withQueryString();

        return view('panic.index', compact('panicButtons'));
    }

    public function create()
    {
        return view('panic.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|in:kebakaran,medis,kriminal,bencana,lainnya',
            'keterangan' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'alamat_lokasi' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('panic', 'public');
        }

        $validated['user_id'] = auth()->id();
        $validated['rt_id'] = auth()->user()->rt_id;
        $validated['status'] = 'baru';

        $panic = PanicButton::create($validated);

        return redirect()->route('panic.show', $panic)
            ->with('success', '🚨 Sinyal darurat terkirim! Petugas akan segera datang.');
    }

    public function show(PanicButton $panic)
    {
        $panic->load(['user', 'rt', 'penangan']);
        return view('panic.show', compact('panic'));
    }

    public function edit(PanicButton $panic)
    {
        return view('panic.edit', compact('panic'));
    }

    public function update(Request $request, PanicButton $panic)
    {
        $validated = $request->validate([
            'status' => 'required|in:baru,ditangani,selesai,false_alarm',
            'catatan_penanganan' => 'nullable|string',
        ]);

        if ($validated['status'] === 'ditangani' && !$panic->ditangani_at) {
            $validated['ditangani_at'] = now();
            $validated['ditangani_oleh'] = auth()->id();
        }

        if (in_array($validated['status'], ['selesai', 'false_alarm'])) {
            $validated['selesai_at'] = now();
        }

        $panic->update($validated);

        return back()->with('success', 'Status panic button diperbarui.');
    }

    public function destroy(PanicButton $panic)
    {
        $panic->delete();
        return redirect()->route('panic.index')->with('success', 'Data dihapus.');
    }
}