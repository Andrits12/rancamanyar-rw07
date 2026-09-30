<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use App\Models\Lamaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LamaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Lamaran::with(['lowongan', 'user', 'rt', 'verifikator']);
        $user = auth()->user();

        // Filter berdasarkan role
        if ($user->hasRole('warga') && !$user->hasAnyRole(['super_admin', 'ketua_rw', 'ketua_rt', 'sekretaris'])) {
            // Warga: hanya lihat lamaran sendiri
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('ketua_rt') && $user->rt_id && !$user->hasAnyRole(['super_admin', 'ketua_rw'])) {
            // Ketua RT: hanya lihat lamaran dari RT-nya
            $query->where('rt_id', $user->rt_id);
        }
        // RW, super admin, sekretaris: lihat semua

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('lowongan_id')) $query->where('lowongan_id', $request->lowongan_id);

        $lamarans = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('lowongan.lamaran.index', compact('lamarans'));
    }

    public function create(Request $request)
    {
        $lowongan = Lowongan::findOrFail($request->lowongan_id);

        // Cek sudah lamar?
        $sudahLamar = Lamaran::where('lowongan_id', $lowongan->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($sudahLamar) {
            return redirect()->route('lowongan.show', $lowongan)
                ->with('error', 'Anda sudah melamar lowongan ini.');
        }

        return view('lowongan.lamaran.create', compact('lowongan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lowongan_id' => 'required|exists:lowongan,id',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'surat_lamaran' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'portfolio' => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240',
            'pengalaman' => 'nullable|string',
            'motivasi' => 'required|string',
            'no_hp_pelamar' => 'required|string|max:20',
            'email_pelamar' => 'nullable|email|max:100',
        ]);

        // Cek sudah lamar?
        $sudahLamar = Lamaran::where('lowongan_id', $validated['lowongan_id'])
            ->where('user_id', auth()->id())
            ->exists();

        if ($sudahLamar) {
            return back()->with('error', 'Anda sudah melamar lowongan ini.');
        }

        // Upload file
        if ($request->hasFile('cv')) {
            $validated['cv'] = $request->file('cv')->store('lamaran/cv', 'public');
        }
        if ($request->hasFile('surat_lamaran')) {
            $validated['surat_lamaran'] = $request->file('surat_lamaran')->store('lamaran/surat', 'public');
        }
        if ($request->hasFile('portfolio')) {
            $validated['portfolio'] = $request->file('portfolio')->store('lamaran/portfolio', 'public');
        }

        $user = auth()->user();
        $validated['user_id'] = $user->id;
        $validated['warga_id'] = $user->warga_id;
        $validated['rt_id'] = $user->rt_id;
        $validated['status'] = 'diajukan';

        Lamaran::create($validated);

        return redirect()->route('lamaran.index')
            ->with('success', 'Lamaran berhasil dikirim. Menunggu verifikasi RT.');
    }

    public function show(Lamaran $lamaran)
    {
        $lamaran->load(['lowongan', 'user', 'rt', 'verifikator']);
        return view('lowongan.lamaran.show', compact('lamaran'));
    }

    public function edit(Lamaran $lamaran)
    {
        return view('lowongan.lamaran.edit', compact('lamaran'));
    }

    public function update(Request $request, Lamaran $lamaran)
    {
        $validated = $request->validate([
            'pengalaman' => 'nullable|string',
            'motivasi' => 'required|string',
            'no_hp_pelamar' => 'required|string|max:20',
            'email_pelamar' => 'nullable|email|max:100',
        ]);

        $lamaran->update($validated);

        return redirect()->route('lamaran.show', $lamaran)->with('success', 'Lamaran diperbarui.');
    }

    public function destroy(Lamaran $lamaran)
    {
        if ($lamaran->user_id !== auth()->id() && !auth()->user()->hasRole('super_admin')) {
            return back()->with('error', 'Tidak bisa menghapus lamaran orang lain.');
        }

        if ($lamaran->status !== 'diajukan' && !auth()->user()->hasRole('super_admin')) {
            return back()->with('error', 'Lamaran yang sudah diproses tidak bisa dihapus.');
        }

        $lamaran->delete();
        return redirect()->route('lamaran.index')->with('success', 'Lamaran dihapus.');
    }

    /**
     * RT verifikasi lamaran
     */
    public function verifikasi(Request $request, Lamaran $lamaran)
    {
        $request->validate([
            'catatan_verifikasi' => 'nullable|string',
            'is_direkomendasikan' => 'nullable|boolean',
        ]);

        $lamaran->update([
            'status' => 'diverifikasi_rt',
            'verifikator_id' => auth()->id(),
            'verifikasi_at' => now(),
            'catatan_verifikasi' => $request->catatan_verifikasi,
            'is_direkomendasikan' => $request->boolean('is_direkomendasikan'),
        ]);

        return back()->with('success', 'Lamaran diverifikasi.');
    }

    /**
     * Teruskan ke perusahaan
     */
    public function teruskan(Lamaran $lamaran)
    {
        if (!in_array($lamaran->status, ['diajukan', 'diverifikasi_rt'])) {
            return back()->with('error', 'Status tidak valid untuk diteruskan.');
        }

        $lamaran->update([
            'status' => 'diteruskan',
            'verifikator_id' => auth()->id(),
            'verifikasi_at' => now(),
        ]);

        return back()->with('success', 'Lamaran diteruskan ke perusahaan.');
    }

    /**
     * Update status (interview, diterima, ditolak)
     */
    public function updateStatus(Request $request, Lamaran $lamaran)
    {
        $request->validate([
            'status' => 'required|in:diajukan,diverifikasi_rt,diteruskan,interview,diterima,ditolak',
            'catatan_verifikasi' => 'nullable|string',
        ]);

        $lamaran->update([
            'status' => $request->status,
            'catatan_verifikasi' => $request->catatan_verifikasi ?? $lamaran->catatan_verifikasi,
        ]);

        return back()->with('success', 'Status lamaran diperbarui.');
    }
}