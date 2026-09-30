<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keluarga extends Model
{
    protected $table = 'keluarga';

    protected $fillable = [
        'no_kk',
        'rt_id',
        'kepala_keluarga_nama',
        'alamat',
        'status_rumah',
        'jumlah_anggota',
        'status_keluarga',
        'tgl_daftar',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tgl_daftar' => 'date',
        ];
    }

    // ============ RELASI ============
    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function wargas()
    {
        return $this->hasMany(Warga::class);
    }

    public function kepalaKeluarga()
    {
        return $this->hasOne(Warga::class)->where('status_keluarga', 'kepala_keluarga');
    }

    // ============ HELPER ============
    public function updateJumlahAnggota()
    {
        $this->update([
            'jumlah_anggota' => $this->wargas()->where('status_hidup', 'hidup')->count(),
        ]);
    }
}