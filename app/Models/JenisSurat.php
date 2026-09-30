<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisSurat extends Model
{
    protected $table = 'jenis_surat';

    protected $fillable = [
        'kode', 'nama', 'deskripsi', 'syarat', 'template', 'is_active', 'urutan',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function surats()
    {
        return $this->hasMany(Surat::class);
    }
}