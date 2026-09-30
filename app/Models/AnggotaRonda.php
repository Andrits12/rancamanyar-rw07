<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnggotaRonda extends Model
{
    protected $table = 'anggota_ronda';

    protected $fillable = ['jadwal_ronda_id', 'user_id', 'warga_id', 'nama_manual', 'hadir', 'absen_at', 'catatan'];

    protected function casts(): array
    {
        return ['hadir' => 'boolean', 'absen_at' => 'datetime'];
    }

    public function jadwal() { return $this->belongsTo(JadwalRonda::class, 'jadwal_ronda_id'); }
    public function user() { return $this->belongsTo(User::class); }
    public function warga() { return $this->belongsTo(Warga::class); }
}