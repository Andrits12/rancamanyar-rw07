<?php

namespace App\Http\Controllers;

use App\Models\Kematian;
use App\Models\Warga;
use App\Models\Rt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KematianController extends Controller
{
    public function index(Request $request)
    {
        $query = Kematian::with(['warga', 'rt', 'pencatat']);

        if (auth()->user()->hasRole('ketua_rt') && auth()->user()->rt_id && !auth()->user()->hasAnyRole(['super_admin', 'ketua_rw', 'sekretaris'])) {
            $query->where('rt_id', auth()->user()->rt_id);
        }

        if ($request->filled('rt_id')) $query->where('rt_id', $request->rt_id);
        if ($request->filled('tahun')) $query->whereYear('tanggal_meninggal', $request->tahun);
        if ($request->filled('sebab')) $query->where('sebab', $request->sebab);

        $kematians = $query->orderBy('tanggal_meninggal', 'desc')->paginate(15)->withQueryString();
        $rts = Rt::orderBy('nomor_rt')->get();

        $stats = [
            'total' => (clone $query)->count(),
            'bulan_ini' => Kematian::whereMonth('tanggal_meninggal', now()->month)
                ->whereYear('tanggal_meninggal', now()->year)
                ->when(auth()->user()->hasRole('ketua_rt') && auth()->user()->rt_id, fn($q) => $q->where('rt_id', auth()->user()->rt_id))
                ->count(),
            'tahun_ini' => Kematian::whereYear('tanggal_meninggal', now()->year)
                ->when(auth()->user()->hasRole('ketua_rt') && auth()->user()->rt_id, fn($q) => $q->where('rt_id', auth()->user()->rt_id))
                ->count(),
        ];

        return view('akta.kematian.index', compact('kematians', 'rts', 'stats'));
    }

    public function create(Request $request)
    {
        $rts = Rt::orderBy('nomor_rt')->get();

        // Kalau ada warga_id di URL
        $selectedWarga = null;
        if ($request->filled('warga_id')) {
            $selectedWarga = Warga::with('keluarga')->find($request->warga_id);
        }

        // Ambil warga hidup untuk dropdown
        $wargas = Warga::with(['keluarga', 'rt'])
            ->where('status_hidup', 'hidup')
            ->when(auth()->user()->hasRole('ketua_rt') && auth()->user()->rt_id, fn($q) => $q->where('rt_id', auth()->user()->rt_id))
            ->orderBy('nama')
            ->get();

        return view('akta.kematian.create', compact('rts', 'wargas', 'selectedWarga'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warga_id' => 'required|exists:warga,id',
            'tanggal_meninggal' => 'required|date',
            'jam_meninggal' => 'nullable',
            'tempat_meninggal' => 'nullable|string|max:150',
            'sebab' => 'required|in:sakit,kecelakaan,usia_lanjut,wabah,lainnya',
            'keterangan_sebab' => 'nullable|string',
            'tempat_pemakaman' => 'nullable|string|max:150',
            'tanggal_pemakaman' => 'nullable|date',
            'no_akta_kematian' => 'nullable|string|max:50',
            'tanggal_akta' => 'nullable|date',
            'dokumen_akta' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->hasFile('dokumen_akta')) {
            $validated['dokumen_akta'] = $request->file('dokumen_akta')->store('akta/kematian', 'public');
        }

        DB::beginTransaction();
        try {
            $warga = Warga::findOrFail($validated['warga_id']);

            $validated['keluarga_id'] = $warga->keluarga_id;
            $validated['rt_id'] = $warga->rt_id;
            $validated['dicatat_oleh'] = auth()->id();

            // Buat data kematian
            $kematian = Kematian::create($validated);

            // Update status warga
            $warga->update([
                'status_hidup' => 'meninggal',
                'tgl_meninggal' => $validated['tanggal_meninggal'],
            ]);

            // Update jumlah anggota keluarga
            $warga->keluarga?->updateJumlahAnggota();

            DB::commit();

            return redirect()->route('kematian.show', $kematian)
                ->with('success', 'Data kematian berhasil dicatat. Status warga diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Kematian $kematian)
    {
        $kematian->load(['warga.keluarga', 'rt', 'pencatat']);
        return view('akta.kematian.show', compact('kematian'));
    }

    public function edit(Kematian $kematian)
    {
        $rts = Rt::orderBy('nomor_rt')->get();
        return view('akta.kematian.edit', compact('kematian', 'rts'));
    }

    public function update(Request $request, Kematian $kematian)
    {
        $validated = $request->validate([
            'tanggal_meninggal' => 'required|date',
            'jam_meninggal' => 'nullable',
            'tempat_meninggal' => 'nullable|string|max:150',
            'sebab' => 'required|in:sakit,kecelakaan,usia_lanjut,wabah,lainnya',
            'keterangan_sebab' => 'nullable|string',
            'tempat_pemakaman' => 'nullable|string|max:150',
            'tanggal_pemakaman' => 'nullable|date',
            'no_akta_kematian' => 'nullable|string|max:50',
            'tanggal_akta' => 'nullable|date',
            'dokumen_akta' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->hasFile('dokumen_akta')) {
            if ($kematian->dokumen_akta && Storage::disk('public')->exists($kematian->dokumen_akta)) {
                Storage::disk('public')->delete($kematian->dokumen_akta);
            }
            $validated['dokumen_akta'] = $request->file('dokumen_akta')->store('akta/kematian', 'public');
        }

        $kematian->update($validated);

        // Update status warga juga
        $kematian->warga?->update([
            'status_hidup' => 'meninggal',
            'tgl_meninggal' => $validated['tanggal_meninggal'],
        ]);

        return redirect()->route('kematian.show', $kematian)->with('success', 'Data diperbarui.');
    }

    public function destroy(Kematian $kematian)
    {
        DB::beginTransaction();
        try {
            // Kembalikan status warga
            $kematian->warga?->update([
                'status_hidup' => 'hidup',
                'tgl_meninggal' => null,
            ]);

            if ($kematian->dokumen_akta && Storage::disk('public')->exists($kematian->dokumen_akta)) {
                Storage::disk('public')->delete($kematian->dokumen_akta);
            }

            $kematian->delete();

            DB::commit();
            return redirect()->route('kematian.index')->with('success', 'Data dihapus & status warga dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}