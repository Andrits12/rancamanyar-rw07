<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelahiran extends Model
{
    protected $table = 'kelahiran';

    protected $fillable = [
        'kode_kelahiran', 'nama_bayi', 'jenis_kelamin', 'tanggal_lahir',
        'jam_lahir', 'tempat_lahir', 'berat_lahir', 'panjang_lahir', 'kondisi_lahir',
        'nama_ayah', 'nik_ayah', 'nama_ibu', 'nik_ibu',
        'keluarga_id', 'warga_id', 'rt_id',
        'no_akta_kelahiran', 'tanggal_akta', 'dokumen_akta',
        'keterangan', 'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_akta' => 'date',
            'berat_lahir' => 'decimal:2',
            'panjang_lahir' => 'decimal:2',
        ];
    }

    // Relasi
    public function keluarga() { return $this->belongsTo(Keluarga::class); }
    public function warga() { return $this->belongsTo(Warga::class); }
    public function rt() { return $this->belongsTo(Rt::class); }
    public function pencatat() { return $this->belongsTo(User::class, 'dicatat_oleh'); }

    // Auto kode
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($k) {
            if (empty($k->kode_kelahiran)) {
                $k->kode_kelahiran = static::generateKode();
            }
        });
    }

    protected static function generateKode()
    {
        $tanggal = now()->format('Ymd');
        $prefix = 'KLR-' . $tanggal . '-';

        $last = static::where('kode_kelahiran', 'like', $prefix . '%')
            ->orderBy('kode_kelahiran', 'desc')->first();

        $nomor = $last ? ((int) substr($last->kode_kelahiran, -3)) + 1 : 1;

        return $prefix . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    // Accessor
    public function getJenisKelaminLabelAttribute()
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    public function getKondisiBadgeAttribute()
    {
        return match ($this->kondisi_lahir) {
            'normal' => 'bg-success',
            'prematur' => 'bg-warning text-dark',
            'cacat' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}