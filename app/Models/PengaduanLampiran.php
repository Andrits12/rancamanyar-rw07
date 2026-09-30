<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaduanLampiran extends Model
{
    protected $table = 'pengaduan_lampiran';

    protected $fillable = ['pengaduan_id', 'tipe', 'file', 'nama_asli'];

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class);
    }

    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file);
    }
}