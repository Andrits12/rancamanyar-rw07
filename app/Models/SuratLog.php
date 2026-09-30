<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratLog extends Model
{
    protected $table = 'surat_log';

    protected $fillable = ['surat_id', 'user_id', 'aksi', 'catatan'];

    public function surat()
    {
        return $this->belongsTo(Surat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}