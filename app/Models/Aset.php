<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    protected $table = 'aset';

    protected $fillable = [
        'kode_aset', 'pemilik', 'rt_id', 'nama', 'kategori', 'deskripsi',
        'jumlah_total', 'satuan', 'kondisi',
        'lokasi_penyimpanan', 'tanggal_perolehan', 'harga_perolehan',
        'sumber_perolehan', 'foto', 'is_active', 'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_perolehan' => 'date',
            'harga_perolehan' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // ============ RELASI ============
    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function peminjamans()
    {
        return $this->hasMany(PeminjamanAset::class);
    }

    public function peminjamanAktif()
    {
        return $this->hasMany(PeminjamanAset::class)
            ->whereIn('status', ['disetujui', 'dipinjam', 'terlambat']);
    }

    // ============ AUTO KODE ============
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($a) {
            if (empty($a->kode_aset)) {
                $a->kode_aset = static::generateKode();
            }
        });
    }

    protected static function generateKode()
    {
        $tahun = now()->format('Y');
        $prefix = 'AST-' . $tahun . '-';

        $last = static::where('kode_aset', 'like', $prefix . '%')
            ->orderBy('kode_aset', 'desc')
            ->first();

        $nomor = $last ? ((int) substr($last->kode_aset, -3)) + 1 : 1;

        return $prefix . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    // ============ ACCESSOR ============
    public function getJumlahDipinjamAttribute()
    {
        return $this->peminjamanAktif()->sum('jumlah');
    }

    public function getJumlahTersediaAttribute()
    {
        return max(0, $this->jumlah_total - $this->jumlah_dipinjam);
    }

    public function getKategoriLabelAttribute()
    {
        return match ($this->kategori) {
            'tenda' => '? Tenda',
            'kursi' => '?? Kursi',
            'meja' => '?? Meja',
            'elektronik' => '?? Elektronik',
            'alat_kerja' => '?? Alat Kerja',
            'perlengkapan' => '?? Perlengkapan',
            default => '?? Lainnya',
        };
    }

    public function getKondisiBadgeAttribute()
    {
        return match ($this->kondisi) {
            'baik' => 'bg-success',
            'rusak_ringan' => 'bg-warning text-dark',
            'rusak_berat' => 'bg-danger',
            'hilang' => 'bg-dark',
            default => 'bg-secondary',
        };
    }

    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    public function getPemilikLabelAttribute()
    {
        if ($this->pemilik === 'rw') {
            return 'RW 07';
        }
        return $this->rt->nama_rt ?? 'RT ?';
    }
    
    public function getPemilikBadgeAttribute()
    {
        return $this->pemilik === 'rw' ? 'bg-danger' : 'bg-info text-dark';
    }

    /** Scope: filter berdasarkan user yang login*/
    public function scopeVisibleFor($query, $user)
    {
        // Super admin, ketua RW, sekretaris: lihat semua
        if ($user->hasAnyRole(['super_admin', 'ketua_rw', 'sekretaris'])) {
            return $query;
        }

        // Ketua RT & warga: hanya lihat aset RT-nya + aset RW
        if ($user->rt_id) {
            return $query->where(function ($q) use ($user) {
                $q->where('pemilik', 'rw')
                    ->orWhere(function ($sub) use ($user) {
                        $sub->where('pemilik', 'rt')
                            ->where('rt_id', $user->rt_id);
                });
            });
        }

        // User tanpa RT: hanya lihat aset RW
        return $query->where('pemilik', 'rw');
    }

}