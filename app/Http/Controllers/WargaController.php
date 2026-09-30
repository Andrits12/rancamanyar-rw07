<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\Keluarga;
use App\Models\Rt;
use Illuminate\Http\Request;

class WargaController extends Controller
{
    /**
     * Daftar warga (dengan filter)
     */
    public function index(Request $request)
    {
        $query = Warga::with(['keluarga', 'rt'])->where('status_hidup', 'hidup');

        // Filter otomatis: Kalau Ketua RT, hanya lihat RT-nya
        if (auth()->user()->hasRole('ketua_rt') && auth()->user()->rt_id) {
            $query->where('rt_id', auth()->user()->rt_id);
        }

        if ($request->filled('rt_id')) {
            $query->where('rt_id', $request->rt_id);
        }

        if ($request->filled('jk')) {
            $query->where('jenis_kelamin', $request->jk);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nik', 'like', "%{$q}%")
                    ->orWhere('nama', 'like', "%{$q}%");
            });
        }

        $wargas = $query->orderBy('nama')->paginate(20)->withQueryString();
        $rts = Rt::orderBy('nomor_rt')->get();

        return view('warga.index', compact('wargas', 'rts'));
    }

    /**
     * Form tambah warga (butuh keluarga_id)
     */
    public function create(Request $request)
    {
        $keluarga = null;
        if ($request->filled('keluarga_id')) {
            $keluarga = Keluarga::with('rt')->findOrFail($request->keluarga_id);
        }

        $keluargas = Keluarga::with('rt')->orderBy('kepala_keluarga_nama')->get();
        $rts = Rt::orderBy('nomor_rt')->get();

        return view('warga.create', compact('keluarga', 'keluargas', 'rts'));
    }

    /**
     * Simpan warga baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:warga,nik',
            'nama' => 'required|string|max:100',
            'keluarga_id' => 'required|exists:keluarga,id',
            'rt_id' => 'required|exists:rt,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:100',
            'tgl_lahir' => 'required|date',
            'agama' => 'nullable|string|max:20',
            'pendidikan' => 'nullable|string|max:50',
            'pekerjaan' => 'nullable|string|max:100',
            'status_kawin' => 'nullable|string|max:30',
            'status_keluarga' => 'required|in:kepala_keluarga,istri,anak,menantu,cucu,orang_tua,mertua,famili_lain,lainnya',
            'kewarganegaraan' => 'nullable|string|max:30',
            'golongan_darah' => 'nullable|string|max:5',
            'no_akta_lahir' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
        ]);

        $validated['status_hidup'] = 'hidup';
        $validated['kewarganegaraan'] = $validated['kewarganegaraan'] ?? 'WNI';

        $warga = Warga::create($validated);

        // Update jumlah anggota di keluarga
        $warga->keluarga->updateJumlahAnggota();

        // Update nama kepala keluarga kalau warga ini kepala keluarga
        if ($warga->status_keluarga === 'kepala_keluarga') {
            $warga->keluarga->update(['kepala_keluarga_nama' => $warga->nama]);
        }

        return redirect()->route('keluarga.show', $warga->keluarga_id)
            ->with('success', "Warga {$warga->nama} berhasil ditambahkan.");
    }

    /**
     * Detail warga
     */
    public function show(Warga $warga)
    {
        $warga->load(['keluarga', 'rt', 'user']);
        return view('warga.show', compact('warga'));
    }

    /**
     * Form edit warga
     */
    public function edit(Warga $warga)
    {
        $keluargas = Keluarga::with('rt')->orderBy('kepala_keluarga_nama')->get();
        $rts = Rt::orderBy('nomor_rt')->get();

        return view('warga.edit', compact('warga', 'keluargas', 'rts'));
    }

    /**
     * Update warga
     */
    public function update(Request $request, Warga $warga)
    {
        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:warga,nik,' . $warga->id,
            'nama' => 'required|string|max:100',
            'keluarga_id' => 'required|exists:keluarga,id',
            'rt_id' => 'required|exists:rt,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:100',
            'tgl_lahir' => 'required|date',
            'agama' => 'nullable|string|max:20',
            'pendidikan' => 'nullable|string|max:50',
            'pekerjaan' => 'nullable|string|max:100',
            'status_kawin' => 'nullable|string|max:30',
            'status_keluarga' => 'required|in:kepala_keluarga,istri,anak,menantu,cucu,orang_tua,mertua,famili_lain,lainnya',
            'kewarganegaraan' => 'nullable|string|max:30',
            'golongan_darah' => 'nullable|string|max:5',
            'no_akta_lahir' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
            'status_hidup' => 'required|in:hidup,meninggal,pindah',
        ]);

        $keluargaIdOld = $warga->keluarga_id;

        $warga->update($validated);

        // Update jumlah anggota di keluarga lama & baru
        if ($keluargaIdOld != $warga->keluarga_id) {
            Keluarga::find($keluargaIdOld)?->updateJumlahAnggota();
            $warga->keluarga->updateJumlahAnggota();
        } else {
            $warga->keluarga->updateJumlahAnggota();
        }

        return redirect()->route('warga.show', $warga)
            ->with('success', "Data warga {$warga->nama} berhasil diperbarui.");
    }

    /**
     * Hapus warga
     */
    public function destroy(Warga $warga)
    {
        $keluarga = $warga->keluarga;
        $nama = $warga->nama;

        $warga->delete();

        $keluarga?->updateJumlahAnggota();

        return redirect()->route('keluarga.show', $keluarga)
            ->with('success', "Warga {$nama} berhasil dihapus.");
    }
}