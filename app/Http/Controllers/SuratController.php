<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\SuratLog;
use App\Models\JenisSurat;
use App\Models\Rt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuratController extends Controller
{
    public function index(Request $request)
    {
        $query = Surat::with(['jenisSurat', 'user', 'rt']);
        $user = auth()->user();

        // Filter otomatis berdasarkan role
        if ($user->hasRole('warga') && !$user->hasAnyRole(['super_admin', 'ketua_rw', 'ketua_rt', 'sekretaris'])) {
            // Warga: hanya lihat surat sendiri
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('ketua_rt') && $user->rt_id) {
            // Ketua RT: lihat surat RT-nya
            $query->where('rt_id', $user->rt_id);
        }
        // Super Admin, Ketua RW, Sekretaris: lihat semua

        // Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis_surat_id', $request->jenis);
        }
        if ($request->filled('q')) {
            $query->where(function ($sub) use ($request) {
                $sub->where('kode_surat', 'like', '%' . $request->q . '%')
                    ->orWhere('keperluan', 'like', '%' . $request->q . '%');
            });
        }

        $surats = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $jenisSurats = JenisSurat::where('is_active', true)->orderBy('urutan')->get();

        return view('surat.index', compact('surats', 'jenisSurats'));
    }

    public function create()
    {
        $jenisSurats = JenisSurat::where('is_active', true)->orderBy('urutan')->get();
        return view('surat.create', compact('jenisSurats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_surat_id' => 'required|exists:jenis_surat,id',
            'keperluan' => 'required|string',
            'catatan_pemohon' => 'nullable|string',
            'data_tambahan' => 'nullable|array',
        ]);

        $user = auth()->user();

        DB::beginTransaction();
        try {
            $surat = Surat::create([
                'jenis_surat_id' => $validated['jenis_surat_id'],
                'user_id' => $user->id,
                'warga_id' => $user->warga_id,
                'rt_id' => $user->rt_id ?? 1,
                'keperluan' => $validated['keperluan'],
                'catatan_pemohon' => $validated['catatan_pemohon'] ?? null,
                'data_tambahan' => $validated['data_tambahan'] ?? null,
                'status' => 'diajukan',
            ]);

            SuratLog::create([
                'surat_id' => $surat->id,
                'user_id' => $user->id,
                'aksi' => 'diajukan',
                'catatan' => 'Surat diajukan oleh pemohon',
            ]);

            DB::commit();

            return redirect()->route('surat.show', $surat)
                ->with('success', "Surat berhasil diajukan. Kode: {$surat->kode_surat}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Surat $surat)
    {
        $surat->load(['jenisSurat', 'user', 'warga', 'rt', 'verifikatorRt', 'penyetujuRw', 'logs.user']);
        $surat->increment('views');
        return view('surat.show', compact('surat'));
    }

    public function edit(Surat $surat)
    {
        // Hanya pemohon & status masih 'diajukan' yang boleh edit
        if ($surat->user_id !== auth()->id() || $surat->status !== 'diajukan') {
            return back()->with('error', 'Surat tidak dapat diubah.');
        }

        $jenisSurats = JenisSurat::where('is_active', true)->orderBy('urutan')->get();
        return view('surat.edit', compact('surat', 'jenisSurats'));
    }

    public function update(Request $request, Surat $surat)
    {
        if ($surat->user_id !== auth()->id() || $surat->status !== 'diajukan') {
            return back()->with('error', 'Surat tidak dapat diubah.');
        }

        $validated = $request->validate([
            'keperluan' => 'required|string',
            'catatan_pemohon' => 'nullable|string',
        ]);

        $surat->update($validated);

        return redirect()->route('surat.show', $surat)->with('success', 'Surat diperbarui.');
    }

    public function destroy(Surat $surat)
    {
        if ($surat->status !== 'diajukan' && !auth()->user()->hasRole('super_admin')) {
            return back()->with('error', 'Surat yang sudah diproses tidak dapat dihapus.');
        }

        $surat->delete();
        return redirect()->route('surat.index')->with('success', 'Surat dihapus.');
    }

    /**
     * Approve surat oleh RT
     */
    public function approveRt(Request $request, Surat $surat)
    {
        $request->validate(['catatan' => 'nullable|string']);

        if ($surat->status !== 'diajukan') {
            return back()->with('error', 'Status surat tidak valid untuk diverifikasi.');
        }

        DB::beginTransaction();
        try {
            $surat->update([
                'status' => 'verifikasi_rw',
                'verifikator_rt_id' => auth()->id(),
                'verifikasi_rt_at' => now(),
                'catatan_rt' => $request->catatan,
            ]);

            SuratLog::create([
                'surat_id' => $surat->id,
                'user_id' => auth()->id(),
                'aksi' => 'verifikasi_rt',
                'catatan' => $request->catatan ?? 'Diverifikasi oleh RT',
            ]);

            DB::commit();
            return back()->with('success', 'Surat diverifikasi & diteruskan ke RW.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Tolak surat oleh RT
     */
    public function tolakRt(Request $request, Surat $surat)
    {
        $request->validate(['catatan' => 'required|string']);

        DB::beginTransaction();
        try {
            $surat->update([
                'status' => 'ditolak_rt',
                'verifikator_rt_id' => auth()->id(),
                'verifikasi_rt_at' => now(),
                'catatan_rt' => $request->catatan,
            ]);

            SuratLog::create([
                'surat_id' => $surat->id,
                'user_id' => auth()->id(),
                'aksi' => 'ditolak_rt',
                'catatan' => $request->catatan,
            ]);

            DB::commit();
            return back()->with('success', 'Surat ditolak oleh RT.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Approve surat oleh RW (final)
     */
    public function approveRw(Request $request, Surat $surat)
    {
        $request->validate(['catatan' => 'nullable|string']);

        if ($surat->status !== 'verifikasi_rw') {
            return back()->with('error', 'Status surat tidak valid.');
        }

        DB::beginTransaction();
        try {
            $nomorSurat = $this->generateNomorSurat($surat);

            $surat->update([
                'status' => 'selesai',
                'penyetuju_rw_id' => auth()->id(),
                'approval_rw_at' => now(),
                'catatan_rw' => $request->catatan,
                'selesai_at' => now(),
                'tanggal_surat' => now()->toDateString(),
                'nomor_surat' => $nomorSurat,
            ]);

            SuratLog::create([
                'surat_id' => $surat->id,
                'user_id' => auth()->id(),
                'aksi' => 'selesai',
                'catatan' => $request->catatan ?? 'Disetujui & ditandatangani RW',
            ]);

            DB::commit();
            return back()->with('success', 'Surat disetujui. Nomor: ' . $nomorSurat);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function tolakRw(Request $request, Surat $surat)
    {
        $request->validate(['catatan' => 'required|string']);

        DB::beginTransaction();
        try {
            $surat->update([
                'status' => 'ditolak_rw',
                'penyetuju_rw_id' => auth()->id(),
                'approval_rw_at' => now(),
                'catatan_rw' => $request->catatan,
            ]);

            SuratLog::create([
                'surat_id' => $surat->id,
                'user_id' => auth()->id(),
                'aksi' => 'ditolak_rw',
                'catatan' => $request->catatan,
            ]);

            DB::commit();
            return back()->with('success', 'Surat ditolak oleh RW.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    protected function generateNomorSurat(Surat $surat)
    {
        $romawi = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        $bulan = $romawi[now()->month - 1];
        $tahun = now()->year;

        $last = Surat::whereYear('tanggal_surat', $tahun)
            ->whereNotNull('nomor_surat')
            ->orderBy('id', 'desc')
            ->first();

        $nomor = $last ? ((int) explode('/', $last->nomor_surat)[0]) + 1 : 1;

        return sprintf('%03d/RW07/%s/%d', $nomor, $bulan, $tahun);
    }
}