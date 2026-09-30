<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaduanTindakLanjut extends Model
{
    protected $table = 'pengaduan_tindak_lanjut';

    protected $fillable = ['pengaduan_id', 'user_id', 'status_lama', 'status_baru', 'catatan'];

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}