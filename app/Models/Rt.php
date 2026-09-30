<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rt extends Model
{
    protected $table = 'rt';

    protected $fillable = [
        'rw_id',
        'nomor_rt',
        'nama_rt',
        'ketua_rt_nama',
        'is_active',
    ];

    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function keluargas()
    {
    return $this->hasMany(Keluarga::class); 
    }

    public function wargas()
    {
    return $this->hasMany(Warga::class);
    }
}