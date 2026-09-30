<?php

namespace App\Http\Controllers;

use App\Models\Kelahiran;
use App\Models\Warga;
use App\Models\Keluarga;
use App\Models\Rt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class KelahiranController extends Controller
{
    public function index(Request $request)
    {
        $query = Kelahiran::with(['rt', 'keluarga', 'pencatat']);

        // Filter otomatis: Ketua RT hanya lihat RT-nya
        if (auth()->user()->hasRole('ketua_rt') && auth()->user()->rt_id && !auth()->user()->hasAnyRole(['super_admin', 'ketua_rw', 'sekretaris'])) {
            $query->where('rt_id', auth()->user()->rt_id);
        }

        if ($request->filled('rt_id')) $query->where('rt_id', $request->rt_id);
        if ($request->filled('tahun')) $query->whereYear('tanggal_lahir', $request->tahun);
        if ($request->filled('q')) {
            $query->where('nama_bayi', 'like', '%' . $request->q . '%');
        }

        $kelahirans = $query->orderBy('tanggal_lahir', 'desc')->paginate(15)->withQueryString();
        $rts = Rt::orderBy('nomor_rt')->get();

        // Statistik
        $stats = [
            'total' => (clone $query)->count(),
            'bulan_ini' => Kelahiran::whereMonth('tanggal_lahir', now()->month)
                ->whereYear('tanggal_lahir', now()->year)
                ->when(auth()->user()->hasRole('ketua_rt') && auth()->user()->rt_id, fn($q) => $q->where('rt_id', auth()->user()->rt_id))
                ->count(),
            'tahun_ini' => Kelahiran::whereYear('tanggal_lahir', now()->year)
                ->when(auth()->user()->hasRole('ketua_rt') && auth()->user()->rt_id, fn($q) => $q->where('rt_id', auth()->user()->rt_id))
                ->count(),
        ];

        return view('akta.kelahiran.index', compact('kelahirans', 'rts', 'stats'));
    }

    public function create()
    {
        $rts = Rt::orderBy('nomor_rt')->get();
        $keluargas = Keluarga::with('rt')->orderBy('kepala_keluarga_nama')->get();
        return view('akta.kelahiran.create', compact('rts', 'keluargas'));
    }
}
    public function store(Request $request)
{
    $validated = $request->validate([
        'nama_bayi' => 'required|string|max:100',
        'jenis_kelamin' => 'required|in:L,P',
        'tanggal_lahir' => 'required|date',
        'jam_lahir' => 'nullable',
        'tempat_lahir' => 'nullable|string|max:100',
        'berat_lahir' => 'nullable|numeric|min:0|max:10',
        'panjang_lahir' => 'nullable|numeric|min:0|max:100',
        'kondisi_lahir' => 'required|in:normal,prematur,cacat,lainnya',
        'nama_ayah' => 'required|string|max:100',
        'nik_ayah' => 'nullable|string|size:16',
        'nama_ibu' => 'required|string|max:100',
        'nik_ibu' => 'nullable|string|size:16',
        'keluarga_id' => 'nullable|exists:keluarga,id',
        'rt_id' => 'required|exists:rt,id',
        'nik_bayi' => 'nullable|string|size:16|unique:warga,nik',
        'no_akta_kelahiran' => 'nullable|string|max:50',
        'tanggal_akta' => 'nullable|date',
        'dokumen_akta' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'keterangan' => 'nullable|string',
        'tambah_ke_warga' => 'nullable|boolean',
    ]);

    if ($request->hasFile('dokumen_akta')) {
        $validated['dokumen_akta'] = $request->file('dokumen_akta')->store('akta/kelahiran', 'public');
    }

    $validated['dicatat_oleh'] = auth()->id();

    DB::beginTransaction();
    try {
        // Simpan data kelahiran
        $kelahiran = Kelahiran::create($validated);

        // OTOMATIS: tambah ke warga kalau diminta
        if ($request->boolean('tambah_ke_warga', true) && $kelahiran->keluarga_id) {
            // Buat NIK sementara (kalau NIK belum ada)
            $nikSementara = $validated['nik_bayi'] ?? '0' . str_pad(random_int(0, 999999999999999), 15, '0', STR_PAD_LEFT);

            // Cek NIK unik
            while (Warga::where('nik', $nikSementara)->exists()) {
                $nikSementara = '0' . str_pad(random_int(0, 999999999999999), 15, '0', STR_PAD_LEFT);
            }

            $wargaBaru = Warga::create([
                'nik' => $nikSementara,
                'nama' => $kelahiran->nama_bayi,
                'keluarga_id' => $kelahiran->keluarga_id,
                'rt_id' => $kelahiran->rt_id,
                'jenis_kelamin' => $kelahiran->jenis_kelamin,
                'tempat_lahir' => $kelahiran->tempat_lahir,
                'tgl_lahir' => $kelahiran->tanggal_lahir,
                'status_keluarga' => 'anak',
                'kewarganegaraan' => 'WNI',
                'status_hidup' => 'hidup',
                'no_akta_lahir' => $kelahiran->no_akta_kelahiran,
            ]);

            // Link balik ke kelahiran
            $kelahiran->update(['warga_id' => $wargaBaru->id]);

            // Update jumlah anggota keluarga
            $kelahiran->keluarga?->updateJumlahAnggota();
        }

        DB::commit();

        return redirect()->route('kelahiran.show', $kelahiran)
            ->with('success', 'Data kelahiran berhasil dicatat.' . ($kelahiran->warga_id ? ' Bayi otomatis ditambahkan sebagai anggota keluarga.' : ''));
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
    }


    public function show(Kelahiran $kelahiran)
    {
        $kelahiran->load(['rt', 'keluarga', 'warga', 'pencatat']);
        return view('akta.kelahiran.show', compact('kelahiran'));
    }

    public function edit(Kelahiran $kelahiran)
    {
        $rts = Rt::orderBy('nomor_rt')->get();
        $keluargas = Keluarga::with('rt')->orderBy('kepala_keluarga_nama')->get();
        return view('akta.kelahiran.edit', compact('kelahiran', 'rts', 'keluargas'));
    }

    public function update(Request $request, Kelahiran $kelahiran)
    {
        $validated = $request->validate([
            'nama_bayi' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'jam_lahir' => 'nullable',
            'tempat_lahir' => 'nullable|string|max:100',
            'berat_lahir' => 'nullable|numeric|min:0|max:10',
            'panjang_lahir' => 'nullable|numeric|min:0|max:100',
            'kondisi_lahir' => 'required|in:normal,prematur,cacat,lainnya',
            'nama_ayah' => 'required|string|max:100',
            'nik_ayah' => 'nullable|string|size:16',
            'nama_ibu' => 'required|string|max:100',
            'nik_ibu' => 'nullable|string|size:16',
            'keluarga_id' => 'nullable|exists:keluarga,id',
            'rt_id' => 'required|exists:rt,id',
            'no_akta_kelahiran' => 'nullable|string|max:50',
            'tanggal_akta' => 'nullable|date',
            'dokumen_akta' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->hasFile('dokumen_akta')) {
            if ($kelahiran->dokumen_akta && Storage::disk('public')->exists($kelahiran->dokumen_akta)) {
                Storage::disk('public')->delete($kelahiran->dokumen_akta);
            }
            $validated['dokumen_akta'] = $request->file('dokumen_akta')->store('akta/kelahiran', 'public');
        }

        $kelahiran->update($validated);

        return redirect()->route('kelahiran.show', $kelahiran)->with('success', 'Data diperbarui.');
    }

    public function destroy(Kelahiran $kelahiran)
    {
        if ($kelahiran->dokumen_akta && Storage::disk('public')->exists($kelahiran->dokumen_akta)) {
            Storage::disk('public')->delete($kelahiran->dokumen_akta);
        }
        $kelahiran->delete();
        return redirect()->route('kelahiran.index')->with('success', 'Data dihapus.');
    }
}