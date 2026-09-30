<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kematian extends Model
{
    protected $table = 'kematian';

    protected $fillable = [
        'kode_kematian', 'warga_id', 'keluarga_id', 'rt_id',
        'tanggal_meninggal', 'jam_meninggal', 'tempat_meninggal',
        'sebab', 'keterangan_sebab',
        'tempat_pemakaman', 'tanggal_pemakaman',
        'no_akta_kematian', 'tanggal_akta', 'dokumen_akta',
        'keterangan', 'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_meninggal' => 'date',
            'tanggal_pemakaman' => 'date',
            'tanggal_akta' => 'date',
        ];
    }

    public function warga() { return $this->belongsTo(Warga::class); }
    public function keluarga() { return $this->belongsTo(Keluarga::class); }
    public function rt() { return $this->belongsTo(Rt::class); }
    public function pencatat() { return $this->belongsTo(User::class, 'dicatat_oleh'); }

    // Auto kode
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($k) {
            if (empty($k->kode_kematian)) {
                $k->kode_kematian = static::generateKode();
            }
        });
    }

    protected static function generateKode()
    {
        $tanggal = now()->format('Ymd');
        $prefix = 'KMT-' . $tanggal . '-';

        $last = static::where('kode_kematian', 'like', $prefix . '%')
            ->orderBy('kode_kematian', 'desc')->first();

        $nomor = $last ? ((int) substr($last->kode_kematian, -3)) + 1 : 1;

        return $prefix . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    // Accessor
    public function getSebabLabelAttribute()
    {
        return match ($this->sebab) {
            'sakit' => '🏥 Sakit',
            'kecelakaan' => '🚗 Kecelakaan',
            'usia_lanjut' => '👴 Usia Lanjut',
            'wabah' => '⚠️ Wabah',
            default => '❓ Lainnya',
        };
    }
}