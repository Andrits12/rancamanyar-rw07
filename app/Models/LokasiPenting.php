<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LokasiPenting extends Model
{
    protected $table = 'lokasi_penting';

    protected $fillable = [
        'nama', 'kategori', 'deskripsi', 'alamat',
        'latitude', 'longitude', 'rt_id', 'warga_id',
        'foto', 'ikon', 'warna', 'kontak',
        'is_public', 'is_active', 'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'is_public' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // ============ RELASI ============
    public function rt() { return $this->belongsTo(Rt::class); }
    public function warga() { return $this->belongsTo(Warga::class); }
    public function pembuat() { return $this->belongsTo(User::class, 'dibuat_oleh'); }

    // ============ ACCESSOR ============
    public function getKategoriLabelAttribute()
    {
        return match ($this->kategori) {
            'rumah_warga' => '🏠 Rumah Warga',
            'pos_ronda' => '🛡️ Pos Ronda',
            'posyandu' => '👶 Posyandu',
            'masjid' => '🕌 Masjid',
            'sekolah' => '🏫 Sekolah',
            'umkm' => '🏪 UMKM',
            'aset_rw' => '📦 Aset RW',
            'fasilitas_umum' => '🏛️ Fasilitas Umum',
            default => '📍 Lainnya',
        };
    }

    public function getKategoriColorAttribute()
    {
        return match ($this->kategori) {
            'rumah_warga' => '#3b82f6',      // biru
            'pos_ronda' => '#10b981',         // hijau
            'posyandu' => '#ec4899',          // pink
            'masjid' => '#f59e0b',            // kuning
            'sekolah' => '#8b5cf6',           // ungu
            'umkm' => '#f97316',              // oranye
            'aset_rw' => '#ef4444',           // merah
            'fasilitas_umum' => '#06b6d4',    // cyan
            default => '#667eea',             // default
        };
    }

    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    // ============ SCOPES ============
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }
}