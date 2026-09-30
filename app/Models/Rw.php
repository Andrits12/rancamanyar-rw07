<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rw extends Model
{
    protected $table = 'rw';

    protected $fillable = [
        'kode_rw',
        'nama_rw',
        'desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'ketua_rw_nama',
        'alamat_sekretariat',
    ];

    public function rts()
    {
        return $this->hasMany(Rt::class);
    }
}