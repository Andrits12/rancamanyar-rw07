<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalRonda extends Model
{
    protected $table = 'jadwal_ronda';

    protected $fillable = [
        'rt_id', 'tanggal', 'shift', 'jam_mulai', 'jam_selesai',
        'koordinator_id', 'koordinator_nama', 'pos_ronda', 'catatan', 'status',
    ];

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function rt() { return $this->belongsTo(Rt::class); }
    public function koordinator() { return $this->belongsTo(User::class, 'koordinator_id'); }
    public function anggotas() { return $this->hasMany(AnggotaRonda::class); }
    public function laporans() { return $this->hasMany(LaporanRonda::class); }

    public function getShiftLabelAttribute()
    {
        return match ($this->shift) {
            'malam_1' => 'Malam 1 (22:00-00:00)',
            'malam_2' => 'Malam 2 (00:00-02:00)',
            'subuh' => 'Subuh (02:00-04:00)',
            default => $this->shift,
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'draft' => 'bg-secondary',
            'aktif' => 'bg-success',
            'selesai' => 'bg-info text-dark',
            'batal' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}