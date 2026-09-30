<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\PeminjamanAset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PeminjamanAsetController extends Controller
{
    public function index(Request $request)
    {
        $query = PeminjamanAset::with(['aset', 'user', 'rt']);

        // Filter otomatis
        $user = auth()->user();
        if ($user->hasRole('warga') && !$user->hasAnyRole(['super_admin', 'ketua_rw', 'sekretaris'])) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) $query->where('status', $request->status);

        $peminjamans = $query->orderByRaw("FIELD(status, 'diajukan', 'disetujui', 'dipinjam', 'terlambat', 'dikembalikan', 'ditolak')")
            ->orderBy('created_at', 'desc')
            ->paginate(15)->withQueryString();

        return view('inventaris.peminjaman.index', compact('peminjamans'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        // Cek permission: hanya yang punya inventaris.pinjam
        if (!$user->can('inventaris.pinjam')) {
            return back()->with('error', 'Hanya Ketua RT/RW yang bisa mengajukan peminjaman. Warga silakan lapor ke Ketua RT.');
        }

        // Filter aset berdasarkan role
        $query = Aset::where('is_active', true)
        ->whereIn('kondisi', ['baik', 'rusak_ringan']);

        if ($user->hasRole('ketua_rt') && !$user->hasAnyRole(['super_admin', 'ketua_rw', 'sekretaris'])) {
            // Ketua RT: hanya lihat aset RT-nya + aset RW
            $query->where(function ($q) use ($user) {
                $q->where('pemilik', 'rw')
                    ->orWhere(function ($sub) use ($user) {
                        $sub->where('pemilik', 'rt')
                        ->where('rt_id', $user->rt_id);
                    });
            });
        }
        // Super admin, RW, sekretaris: lihat semua

        $asets = $query->orderBy('pemilik')->orderBy('nama')->get();


        // Daftar warga untuk pilihan peminjam
        $wargas = \App\Models\User::where('status_akun', 'aktif')
            ->whereNotNull('rt_id')
            ->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['super_admin', 'ketua_rw', 'ketua_rt', 'sekretaris', 'bendahara']);
            });

        if ($user->hasRole('ketua_rt') && !$user->hasAnyRole(['super_admin', 'ketua_rw', 'sekretaris'])) 
        {
            $wargas->where('rt_id', $user->rt_id);
        }

        $wargas = $wargas->orderBy('name')->get();

        $selectedAset = null;
        if ($request->filled('aset_id')) {
            $selectedAset = Aset::find($request->aset_id);
        }

        return view('inventaris.peminjaman.create', compact('asets', 'wargas', 'selectedAset'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Cek permission
        if (!$user->can('inventaris.pinjam')) {
            return back()->with('error', 'Anda tidak berhak mengajukan peminjaman.');
        }

        $validated = $request->validate([
            'aset_id' => 'required|exists:aset,id',
            'peminjam_id' => 'required|exists:users,id',
            'jumlah' => 'required|integer|min:1',
            'keperluan' => 'required|string',
            'tanggal_pinjam' => 'required|date',
            'tanggal_rencana_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'catatan_peminjam' => 'nullable|string',
        ], [
            'peminjam_id.required' => 'Pilih warga yang akan meminjam.',

        ]);
        $aset = Aset::findOrFail($validated['aset_id']);
        $peminjam = \App\Models\User::findOrFail($validated['peminjam_id']);

        // Validasi Ketua RT: hanya boleh pinjamkan aset RT-nya & warga RT-nya
        if ($user->hasRole('ketua_rt') && !$user->hasAnyRole(['super_admin', 'ketua_rw', 'sekretaris'])) {
            if ($aset->pemilik === 'rt' && $aset->rt_id !== $user->rt_id) {
                return back()->with('error', 'Anda hanya bisa meminjamkan aset RT Anda.')->withInput();
            }
            if ($peminjam->rt_id !== $user->rt_id) {
                return back()->with('error', 'Anda hanya bisa meminjamkan aset untuk warga RT Anda.')->withInput();
            }
        }

        // Cek stok
        if ($validated['jumlah'] > $aset->jumlah_tersedia) {
            return back()->with('error', "Stok tidak cukup. Tersedia: {$aset->jumlah_tersedia} {$aset->satuan}.")
            ->withInput();
        }

        // Simpan peminjaman atas nama peminjam, tapi disetujui langsung oleh Ketua RT
        PeminjamanAset::create([
            'aset_id' => $validated['aset_id'],
            'user_id' => $peminjam->id,
            'warga_id' => $peminjam->warga_id,
            'rt_id' => $peminjam->rt_id,
            'jumlah' => $validated['jumlah'],
            'keperluan' => $validated['keperluan'],
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'tanggal_rencana_kembali' => $validated['tanggal_rencana_kembali'],
            'catatan_peminjam' => $validated['catatan_peminjam'] ?? null,
            'status' => 'disetujui',  // ← langsung disetujui karena RT yang input
            'disetujui_oleh' => $user->id,
            'disetujui_at' => now(),
            'catatan_approval' => 'Diinput langsung oleh ' . $user->name,
        ]);

        return redirect()->route('peminjaman-aset.index')
        ->with('success', 'Peminjaman untuk ' . $peminjam->name . ' berhasil dicatat.');
    }

    public function show(PeminjamanAset $peminjamanAset)
    {
        $peminjamanAset->load(['aset', 'user', 'rt', 'penyetuju']);
        return view('inventaris.peminjaman.show', ['peminjaman' => $peminjamanAset]);
    }

    public function edit(PeminjamanAset $peminjamanAset)
    {
        $asets = Aset::where('is_active', true)->orderBy('nama')->get();
        return view('inventaris.peminjaman.edit', ['peminjaman' => $peminjamanAset, 'asets' => $asets]);
    }

    public function update(Request $request, PeminjamanAset $peminjamanAset)
    {
        $validated = $request->validate([
            'jumlah' => 'required|integer|min:1',
            'keperluan' => 'required|string',
            'tanggal_pinjam' => 'required|date',
            'tanggal_rencana_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'catatan_peminjam' => 'nullable|string',
        ]);

        $peminjamanAset->update($validated);

        return redirect()->route('peminjaman-aset.show', $peminjamanAset)
            ->with('success', 'Peminjaman diperbarui.');
    }

    public function destroy(PeminjamanAset $peminjamanAset)
    {
        if (in_array($peminjamanAset->status, ['dipinjam', 'disetujui'])) {
            return back()->with('error', 'Peminjaman aktif tidak bisa dihapus.');
        }
        $peminjamanAset->delete();
        return redirect()->route('peminjaman-aset.index')->with('success', 'Peminjaman dihapus.');
    }

    /**
     * Approve peminjaman
     */
    public function approve(Request $request, PeminjamanAset $peminjamanAset)
    {
        $request->validate(['catatan_approval' => 'nullable|string']);

        if ($peminjamanAset->status !== 'diajukan') {
            return back()->with('error', 'Status tidak valid.');
        }

        $peminjamanAset->update([
            'status' => 'disetujui',
            'disetujui_oleh' => auth()->id(),
            'disetujui_at' => now(),
            'catatan_approval' => $request->catatan_approval,
        ]);

        return back()->with('success', 'Peminjaman disetujui.');
    }

    /**
     * Tolak peminjaman
     */
    public function tolak(Request $request, PeminjamanAset $peminjamanAset)
    {
        $request->validate(['catatan_approval' => 'required|string']);

        $peminjamanAset->update([
            'status' => 'ditolak',
            'disetujui_oleh' => auth()->id(),
            'disetujui_at' => now(),
            'catatan_approval' => $request->catatan_approval,
        ]);

        return back()->with('success', 'Peminjaman ditolak.');
    }

    /**
     * Tandai aset sedang dipinjam (setelah disetujui)
     */
    public function tandaiDipinjam(PeminjamanAset $peminjamanAset)
    {
        if ($peminjamanAset->status !== 'disetujui') {
            return back()->with('error', 'Hanya status "disetujui" yang bisa diubah.');
        }

        $peminjamanAset->update(['status' => 'dipinjam']);
        return back()->with('success', 'Aset ditandai sedang dipinjam.');
    }

    /**
     * Pengembalian aset
     */
    public function kembalikan(Request $request, PeminjamanAset $peminjamanAset)
    {
        $validated = $request->validate([
            'kondisi_kembali' => 'required|in:baik,rusak_ringan,rusak_berat',
            'catatan_kembali' => 'nullable|string',
            'foto_kembali' => 'nullable|image|max:2048',
        ]);

        if (!in_array($peminjamanAset->status, ['dipinjam', 'terlambat'])) {
            return back()->with('error', 'Status tidak valid untuk pengembalian.');
        }

        if ($request->hasFile('foto_kembali')) {
            $validated['foto_kembali'] = $request->file('foto_kembali')->store('pengembalian', 'public');
        }

        $validated['tanggal_kembali_aktual'] = now()->toDateString();
        $validated['status'] = 'dikembalikan';

        $peminjamanAset->update($validated);

        // Update kondisi aset kalau rusak
        if ($validated['kondisi_kembali'] !== 'baik') {
            $peminjamanAset->aset->update(['kondisi' => $validated['kondisi_kembali']]);
        }

        return back()->with('success', 'Aset berhasil dikembalikan.');
    }
}