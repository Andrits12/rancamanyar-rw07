<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanRonda extends Model
{
    protected $table = 'laporan_ronda';

    protected $fillable = [
        'jadwal_ronda_id', 'user_id', 'kondisi', 'catatan',
        'foto', 'latitude', 'longitude',
    ];

    public function jadwal() { return $this->belongsTo(JadwalRonda::class, 'jadwal_ronda_id'); }
    public function user() { return $this->belongsTo(User::class); }

    public function getKondisiBadgeAttribute()
    {
        return match ($this->kondisi) {
            'aman' => 'bg-success',
            'ada_kejadian' => 'bg-warning text-dark',
            'perlu_tindak_lanjut' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}