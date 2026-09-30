<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaduanEskalasi extends Model
{
    protected $table = 'pengaduan_eskalasi';

    protected $fillable = [
        'pengaduan_id', 'dari_user_id', 'ke_user_id',
        'alasan', 'status', 'catatan_rw', 'diterima_at',
    ];

    protected function casts(): array
    {
        return ['diterima_at' => 'datetime'];
    }

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class);
    }

    public function dariUser()
    {
        return $this->belongsTo(User::class, 'dari_user_id');
    }

    public function keUser()
    {
        return $this->belongsTo(User::class, 'ke_user_id');
    }
}