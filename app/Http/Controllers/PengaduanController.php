<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\PengaduanLampiran;
use App\Models\PengaduanTindakLanjut;
use App\Models\Rt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengaduanController extends Controller
{
    /**
     * Daftar pengaduan
     */
    public function index(Request $request)
    {
        $query = Pengaduan::with(['user', 'rt', 'penangan']);
        $user = auth()->user();
        
        // Filter otomatis berdasarkan role
        if ($user->hasRole('ketua_rt') && $user->rt_id) {
            
            // Ketua RT: hanya lihat pengaduan tingkat RT di RT-nya
            $query->where('rt_id', $user->rt_id);
        } elseif ($user->hasRole('ketua_rw')) {
            
            // Ketua RW: lihat semua pengaduan tingkat RW + pengaduan RT yang dieskalasi
            $query->where(function ($q) {
                $q->where('tingkat', 'rw')
                ->orWhere('status_eskalasi', '!=', 'tidak');
            });
        } elseif ($user->hasRole('warga') && !$user->hasAnyRole(['super_admin', 'sekretaris'])) {
            
            // Warga: hanya lihat pengaduan sendiri
            $query->where('user_id', $user->id);
        }
        // Super Admin & Sekretaris: lihat semua
         
        // Filter manual
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('prioritas')) {
            $query->where('prioritas', $request->prioritas);
        }
        if ($request->filled('q')) {
            $query->where(function ($sub) use ($request) {
                $sub->where('judul', 'like', '%' . $request->q . '%')
                ->orWhere('kode_tiket', 'like', '%' . $request->q . '%');
            });
        }
        
        $pengaduans = $query->orderByRaw("FIELD(prioritas, 'tinggi', 'sedang', 'rendah')")
        ->orderBy('created_at', 'desc')
        ->paginate(15)
        ->withQueryString();
        
        return view('pengaduan.index', compact('pengaduans'));
    }

    /**
     * Form buat pengaduan
     */
    public function create()
    {
        return view('pengaduan.create');
    }

    /**
     * Simpan pengaduan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|in:infrastruktur,keamanan,kebersihan,kesehatan,sosial,lainnya',
            'judul' => 'required|string|max:200',
            'deskripsi' => 'required|string',
            'prioritas' => 'required|in:rendah,sedang,tinggi',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'alamat_lokasi' => 'nullable|string',
            'lampiran.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,webp,mp4,pdf',
            'tingkat' => 'required|in:rt,rw',
        ]);

        DB::beginTransaction();
        try {
            // Tentukan rt_id (dari user atau input manual)
            $rtId = auth()->user()->rt_id;
            if (!$rtId && $request->filled('rt_id')) {
                $rtId = $request->rt_id;
            }
            if (!$rtId) {
                $rtId = 1; // fallback
            }

            $pengaduan = Pengaduan::create([
                'user_id' => auth()->id(),
                'warga_id' => auth()->user()->warga_id,
                'rt_id' => $rtId,
                'kategori' => $validated['kategori'],
                'judul' => $validated['judul'],
                'deskripsi' => $validated['deskripsi'],
                'prioritas' => $validated['prioritas'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'alamat_lokasi' => $validated['alamat_lokasi'] ?? null,
                'status' => 'baru',
                'tingkat' => $validated['tingkat'],
            ]);

            // Upload lampiran
            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran') as $file) {
                    $tipe = str_starts_with($file->getMimeType(), 'video') ? 'video'
                        : ($file->getMimeType() === 'application/pdf' ? 'dokumen' : 'foto');

                    $path = $file->store('pengaduan/' . date('Y/m'), 'public');

                    PengaduanLampiran::create([
                        'pengaduan_id' => $pengaduan->id,
                        'tipe' => $tipe,
                        'file' => $path,
                        'nama_asli' => $file->getClientOriginalName(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('pengaduan.show', $pengaduan)
                ->with('success', "Pengaduan berhasil dikirim. Kode tiket: {$pengaduan->kode_tiket}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Detail pengaduan
     */
    public function show(Pengaduan $pengaduan)
    {
        $pengaduan->load(['user', 'rt', 'penangan', 'lampirans', 'tindakLanjuts.user']);
        return view('pengaduan.show', compact('pengaduan'));
    }

    /**
     * Form edit
     */
    public function edit(Pengaduan $pengaduan)
    {
        // Cegah warga edit pengaduan yang sudah diproses
        if (auth()->user()->hasRole('warga') && $pengaduan->status !== 'baru') {
            return back()->with('error', 'Pengaduan yang sudah diproses tidak dapat diubah.');
        }

        return view('pengaduan.edit', compact('pengaduan'));
    }

    /**
     * Update pengaduan
     */
    public function update(Request $request, Pengaduan $pengaduan)
    {
        $validated = $request->validate([
            'kategori' => 'required|in:infrastruktur,keamanan,kebersihan,kesehatan,sosial,lainnya',
            'judul' => 'required|string|max:200',
            'deskripsi' => 'required|string',
            'prioritas' => 'required|in:rendah,sedang,tinggi',
            'alamat_lokasi' => 'nullable|string',
        ]);

        $pengaduan->update($validated);

        return redirect()->route('pengaduan.show', $pengaduan)
            ->with('success', 'Pengaduan berhasil diperbarui.');
    }

    /**
     * Hapus pengaduan
     */
    public function destroy(Pengaduan $pengaduan)
    {
        // Hapus lampiran
        foreach ($pengaduan->lampirans as $lamp) {
            Storage::disk('public')->delete($lamp->file);
        }

        $pengaduan->delete();

        return redirect()->route('pengaduan.index')
            ->with('success', 'Pengaduan berhasil dihapus.');
    }

    /**
     * Tindak lanjut (ubah status + catatan)
     */
    public function tindakLanjut(Request $request, Pengaduan $pengaduan)
    {
        $validated = $request->validate([
            'status_baru' => 'required|in:baru,diproses,selesai,ditolak',
            'catatan' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $statusLama = $pengaduan->status;

            $update = [
                'status' => $validated['status_baru'],
            ];

            if ($validated['status_baru'] === 'diproses' && !$pengaduan->ditangani_at) {
                $update['ditangani_at'] = now();
                $update['ditangani_oleh'] = auth()->id();
            }

            if ($validated['status_baru'] === 'selesai') {
                $update['selesai_at'] = now();
                $update['catatan_penyelesaian'] = $validated['catatan'];
            }

            $pengaduan->update($update);

            PengaduanTindakLanjut::create([
                'pengaduan_id' => $pengaduan->id,
                'user_id' => auth()->id(),
                'status_lama' => $statusLama,
                'status_baru' => $validated['status_baru'],
                'catatan' => $validated['catatan'],
            ]);

            DB::commit();

            return back()->with('success', 'Tindak lanjut berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
    * Eskalasi pengaduan RT ke RW
    */
    
    public function eskalasi(Request $request, Pengaduan $pengaduan)
    {
        $request->validate([
            'alasan' => 'required|string',
        ]);
        
        // Cek hanya boleh untuk pengaduan tingkat RT
        if ($pengaduan->tingkat !== 'rt') {
            return back()->with('error', 'Hanya pengaduan tingkat RT yang bisa dieskalasi.');
        }
        
        if ($pengaduan->status_eskalasi !== 'tidak') {
            return back()->with('error', 'Pengaduan ini sudah pernah dieskalasi.');
        }

        DB::beginTransaction();
        try {

            // Cari Ketua RW
            $ketuaRw = \App\Models\User::role('ketua_rw')->first();

            // Buat record eskalasi
            \App\Models\PengaduanEskalasi::create([
                'pengaduan_id' => $pengaduan->id,
                'dari_user_id' => auth()->id(),
                'ke_user_id' => $ketuaRw->id ?? null,
                'alasan' => $request->alasan,
                'status' => 'dikirim',
            ]);

            // Update status pengaduan
            $pengaduan->update([
                'status_eskalasi' => 'dieskalasi',
                'status' => 'diproses',
            ]);

            // Buat pengaduan baru di tingkat RW (mirror)
            $pengaduanRw = Pengaduan::create([
                'user_id' => $pengaduan->user_id,
                'warga_id' => $pengaduan->warga_id,
                'rt_id' => $pengaduan->rt_id,
                'kategori' => $pengaduan->kategori,
                'tingkat' => 'rw',
                'judul' => '[Eskalasi RT] ' . $pengaduan->judul,
                'deskripsi' => $pengaduan->deskripsi . "\n\n---\nAlasan eskalasi: " . $request->alasan,
                'prioritas' => $pengaduan->prioritas,
                'latitude' => $pengaduan->latitude,
                'longitude' => $pengaduan->longitude,
                'alamat_lokasi' => $pengaduan->alamat_lokasi,
                'status' => 'baru',
                'status_eskalasi' => 'diterima_rw',
                'pengaduan_asal_id' => $pengaduan->id,
            ]);

            // Copy lampiran
            foreach ($pengaduan->lampirans as $lamp) {
                \App\Models\PengaduanLampiran::create([
                    'pengaduan_id' => $pengaduanRw->id,
                    'tipe' => $lamp->tipe,
                    'file' => $lamp->file,
                    'nama_asli' => $lamp->nama_asli,
                ]);
            }

            DB::commit();

            return back()->with('success', 'Pengaduan berhasil dieskalasi ke RW. Kode tiket RW: ' . $pengaduanRw->kode_tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal eskalasi: ' . $e->getMessage());
        }
    }
}