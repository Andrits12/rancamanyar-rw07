<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    protected $table = 'warga';

    protected $fillable = [
        'nik',
        'nama',
        'keluarga_id',
        'rt_id',
        'user_id',
        'jenis_kelamin',
        'tempat_lahir',
        'tgl_lahir',
        'agama',
        'pendidikan',
        'pekerjaan',
        'status_kawin',
        'status_keluarga',
        'kewarganegaraan',
        'golongan_darah',
        'no_akta_lahir',
        'status_hidup',
        'tgl_meninggal',
        'tgl_pindah',
        'keterangan',
        'foto',
    ];

    protected function casts(): array
    {
        return [
            'tgl_lahir' => 'date',
            'tgl_meninggal' => 'date',
            'tgl_pindah' => 'date',
        ];
    }

    // ============ RELASI ============
    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class);
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ============ ACCESSOR ============
    public function getUmurAttribute()
    {
        if (!$this->tgl_lahir) return null;
        return $this->tgl_lahir->diffInYears(now());
    }

    public function getKategoriUmurAttribute()
    {
        $umur = $this->umur;
        if ($umur === null) return '-';
        if ($umur < 1) return 'Balita';
        if ($umur < 6) return 'Anak';
        if ($umur < 13) return 'Anak Sekolah';
        if ($umur < 18) return 'Remaja';
        if ($umur < 60) return 'Dewasa';
        return 'Lansia';
    }
}