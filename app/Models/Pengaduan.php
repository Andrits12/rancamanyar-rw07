<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $table = 'pengaduan';

    protected $fillable = [
        'kode_tiket', 'user_id', 'rt_id', 'warga_id',
        'kategori', 'tingkat', 'judul', 'deskripsi', 'prioritas',
        'latitude', 'longitude', 'alamat_lokasi',
        'status', 'status_eskalasi', 'pengaduan_asal_id',
        'ditangani_at', 'ditangani_oleh',
        'selesai_at', 'catatan_penyelesaian',
    ];

    protected function casts(): array
    {
        return [
            'ditangani_at' => 'datetime',
            'selesai_at' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    // ============ RELASI ============
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function penangan()
    {
        return $this->belongsTo(User::class, 'ditangani_oleh');
    }

    public function lampirans()
    {
        return $this->hasMany(PengaduanLampiran::class);
    }

    public function tindakLanjuts()
    {
        return $this->hasMany(PengaduanTindakLanjut::class)->orderBy('created_at', 'asc');
    }

    public function eskalasis()
    {
        return $this->hasMany(PengaduanEskalasi::class);
    }
    
    public function pengaduanAsal()
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_asal_id');
    }
    
    public function pengaduanHasilEskalasi()
    {
        return $this->hasMany(Pengaduan::class, 'pengaduan_asal_id');
    }

    // ============ AUTO GENERATE KODE TIKET ============
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($p) {
            if (empty($p->kode_tiket)) {
                $p->kode_tiket = static::generateKodeTiket();
            }
        });
    }

    protected static function generateKodeTiket()
    {
        $tanggal = now()->format('Ymd');
        $prefix = 'PGD-' . $tanggal . '-';

        $last = static::where('kode_tiket', 'like', $prefix . '%')
            ->orderBy('kode_tiket', 'desc')
            ->first();

        $nomor = $last ? ((int) substr($last->kode_tiket, -3)) + 1 : 1;

        return $prefix . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    // ============ ACCESSOR ============
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'baru' => 'bg-primary',
            'diproses' => 'bg-warning text-dark',
            'selesai' => 'bg-success',
            'ditolak' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getPrioritasBadgeAttribute()
    {
        return match ($this->prioritas) {
            'rendah' => 'bg-secondary',
            'sedang' => 'bg-info text-dark',
            'tinggi' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getTingkatBadgeAttribute()
    {
        return $this->tingkat === 'rw' ? 'bg-danger' : 'bg-info text-dark';
    }

    public function getTingkatLabelAttribute()
    {
        return $this->tingkat === 'rw' ? 'RW' : 'RT';
    }
}