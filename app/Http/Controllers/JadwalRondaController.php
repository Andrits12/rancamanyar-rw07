<?php

namespace App\Http\Controllers;

use App\Models\JadwalRonda;
use App\Models\AnggotaRonda;
use App\Models\LaporanRonda;
use App\Models\Rt;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalRondaController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalRonda::with(['rt', 'koordinator'])->withCount('anggotas');

        if (auth()->user()->hasRole('ketua_rt') && auth()->user()->rt_id) {
            $query->where('rt_id', auth()->user()->rt_id);
        }

        if ($request->filled('rt_id')) $query->where('rt_id', $request->rt_id);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('tanggal')) $query->whereDate('tanggal', $request->tanggal);

        $jadwals = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();
        $rts = Rt::orderBy('nomor_rt')->get();

        return view('ronda.index', compact('jadwals', 'rts'));
    }

    public function create()
    {
        $rts = Rt::orderBy('nomor_rt')->get();
        return view('ronda.create', compact('rts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rt_id' => 'required|exists:rt,id',
            'tanggal' => 'required|date',
            'shift' => 'required|in:malam_1,malam_2,subuh',
            'jam_mulai' => 'nullable',
            'jam_selesai' => 'nullable',
            'koordinator_nama' => 'nullable|string|max:100',
            'pos_ronda' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
            'anggota_nama' => 'required|array|min:1',
            'anggota_nama.*' => 'required|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $jadwal = JadwalRonda::create([
                'rt_id' => $validated['rt_id'],
                'tanggal' => $validated['tanggal'],
                'shift' => $validated['shift'],
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $validated['jam_selesai'],
                'koordinator_nama' => $validated['koordinator_nama'] ?? null,
                'pos_ronda' => $validated['pos_ronda'] ?? null,
                'catatan' => $validated['catatan'] ?? null,
                'status' => 'aktif',
            ]);

            foreach ($validated['anggota_nama'] as $nama) {
                $user = User::where('name', 'like', $nama)
                    ->where('rt_id', $validated['rt_id'])
                    ->first();
                
                AnggotaRonda::create([
                    'jadwal_ronda_id' => $jadwal->id,
                    'user_id' => $userId,
                    'warga_id' => $user->warga_id,
                    'nama_manual' => $nama,
                    'hadir' => false,
                ]);
            }

            DB::commit();
            return redirect()->route('ronda.show', $jadwal)
                ->with('success', 'Jadwal ronda berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function show(JadwalRonda $ronda)
    {
        $ronda->load(['rt', 'koordinator', 'anggotas.user', 'laporans.user']);
        return view('ronda.show', compact('ronda'));
    }

    public function edit(JadwalRonda $ronda)
    {
        $rts = Rt::orderBy('nomor_rt')->get();
        $ronda->load('anggotas');
        return view('ronda.edit', compact('ronda', 'rts'));
    }

    public function update(Request $request, JadwalRonda $ronda)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'shift' => 'required|in:malam_1,malam_2,subuh',
            'jam_mulai' => 'nullable',
            'jam_selesai' => 'nullable',
            'koordinator_id' => 'nullable|exists:users,id',
            'pos_ronda' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
            'status' => 'required|in:draft,aktif,selesai,batal',
        ]);

        $ronda->update($validated);
        return redirect()->route('ronda.show', $ronda)->with('success', 'Jadwal diperbarui.');
    }

    public function destroy(JadwalRonda $ronda)
    {
        $ronda->delete();
        return redirect()->route('ronda.index')->with('success', 'Jadwal dihapus.');
    }

    /**
     * Absen kehadiran anggota
     */
    public function absen(Request $request, JadwalRonda $ronda)
    {
        $request->validate(['anggota_id' => 'required|exists:anggota_ronda,id']);

        $anggota = AnggotaRonda::findOrFail($request->anggota_id);
        $anggota->update([
            'hadir' => true,
            'absen_at' => now(),
            'catatan' => $request->catatan,
        ]);

        return back()->with('success', 'Absensi dicatat.');
    }

    /**
     * Submit laporan ronda
     */
    public function lapor(Request $request, JadwalRonda $ronda)
    {
        $validated = $request->validate([
            'kondisi' => 'required|in:aman,ada_kejadian,perlu_tindak_lanjut',
            'catatan' => 'required|string',
            'foto' => 'nullable|image|max:2048',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('ronda', 'public');
        }

        $validated['jadwal_ronda_id'] = $ronda->id;
        $validated['user_id'] = auth()->id();

        LaporanRonda::create($validated);

        return back()->with('success', 'Laporan berhasil dikirim.');
    }

    /**
     * Ambil daftar user dari RT tertentu (untuk AJAX)
     */
    public function getUsersByRt(Request $request)
    {
        $users = User::where('rt_id', $request->rt_id)
            ->where('status_akun', 'aktif')
            ->orderBy('name')
            ->get(['id', 'name', 'no_hp']);

        return response()->json($users);
    }
}