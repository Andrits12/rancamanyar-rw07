<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lamaran extends Model
{
    protected $table = 'lamaran';

    protected $fillable = [
        'kode_lamaran', 'lowongan_id', 'user_id', 'warga_id', 'rt_id',
        'cv', 'surat_lamaran', 'portfolio',
        'pengalaman', 'motivasi', 'no_hp_pelamar', 'email_pelamar',
        'status', 'verifikator_id', 'verifikasi_at', 'catatan_verifikasi',
        'is_direkomendasikan',
    ];

    protected function casts(): array
    {
        return [
            'verifikasi_at' => 'datetime',
            'is_direkomendasikan' => 'boolean',
        ];
    }

    // Relasi
    public function lowongan() { return $this->belongsTo(Lowongan::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function warga() { return $this->belongsTo(Warga::class); }
    public function rt() { return $this->belongsTo(Rt::class); }
    public function verifikator() { return $this->belongsTo(User::class, 'verifikator_id'); }

    // Auto kode
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($l) {
            if (empty($l->kode_lamaran)) {
                $l->kode_lamaran = static::generateKode();
            }
        });
    }

    protected static function generateKode()
    {
        $tanggal = now()->format('Ymd');
        $prefix = 'LMR-' . $tanggal . '-';

        $last = static::where('kode_lamaran', 'like', $prefix . '%')
            ->orderBy('kode_lamaran', 'desc')
            ->first();

        $nomor = $last ? ((int) substr($last->kode_lamaran, -3)) + 1 : 1;

        return $prefix . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    // Accessor
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'diajukan' => 'bg-secondary',
            'diverifikasi_rt' => 'bg-info text-dark',
            'diteruskan' => 'bg-primary',
            'interview' => 'bg-warning text-dark',
            'diterima' => 'bg-success',
            'ditolak' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'diajukan' => 'Diajukan',
            'diverifikasi_rt' => 'Diverifikasi RT',
            'diteruskan' => 'Diteruskan ke PT',
            'interview' => 'Interview',
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak',
            default => $this->status,
        };
    }

    public function getCvUrlAttribute()
    {
        return $this->cv ? asset('storage/' . $this->cv) : null;
    }

    public function getSuratLamaranUrlAttribute()
    {
        return $this->surat_lamaran ? asset('storage/' . $this->surat_lamaran) : null;
    }
    
    public function getPortfolioUrlAttribute()
    {
        return $this->portfolio ? asset('storage/' . $this->portfolio) : null;
    }
}