<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PanicButton extends Model
{
    protected $table = 'panic_button';

    protected $fillable = [
        'user_id', 'rt_id', 'jenis', 'keterangan',
        'latitude', 'longitude', 'alamat_lokasi', 'foto',
        'status', 'ditangani_oleh', 'ditangani_at', 'selesai_at', 'catatan_penanganan',
    ];

    protected function casts(): array
    {
        return [
            'ditangani_at' => 'datetime',
            'selesai_at' => 'datetime',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function rt() { return $this->belongsTo(Rt::class); }
    public function penangan() { return $this->belongsTo(User::class, 'ditangani_oleh'); }

    public function getJenisLabelAttribute()
    {
        return match ($this->jenis) {
            'kebakaran' => '🔥 Kebakaran',
            'medis' => '🚑 Medis',
            'kriminal' => '🚨 Kriminal',
            'bencana' => '⚠️ Bencana',
            default => '❓ Lainnya',
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'baru' => 'bg-danger',
            'ditangani' => 'bg-warning text-dark',
            'selesai' => 'bg-success',
            'false_alarm' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }
}