<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lowongan extends Model
{
    protected $table = 'lowongan';

    protected $fillable = [
        'kode_lowongan', 'judul', 'perusahaan', 'deskripsi',
        'kualifikasi', 'tanggung_jawab', 'jenis', 'lokasi',
        'gaji_min', 'gaji_max',
        'kontak_nama', 'kontak_hp', 'kontak_email',
        'tanggal_buka', 'deadline', 'status', 'is_pinned',
        'dibuat_oleh', 'views', 'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_buka' => 'date',
            'deadline' => 'date',
            'is_pinned' => 'boolean',
        ];
    }

    // Relasi
    public function pembuat() { return $this->belongsTo(User::class, 'dibuat_oleh'); }
    public function lamarans() { return $this->hasMany(Lamaran::class); }

    // Auto kode
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($l) {
            if (empty($l->kode_lowongan)) {
                $l->kode_lowongan = static::generateKode();
            }
        });
    }

    protected static function generateKode()
    {
        $tanggal = now()->format('Ymd');
        $prefix = 'LWK-' . $tanggal . '-';

        $last = static::where('kode_lowongan', 'like', $prefix . '%')
            ->orderBy('kode_lowongan', 'desc')
            ->first();

        $nomor = $last ? ((int) substr($last->kode_lowongan, -3)) + 1 : 1;

        return $prefix . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif')
            ->where(function ($q) {
                $q->whereNull('deadline')
                  ->orWhere('deadline', '>=', now()->toDateString());
            });
    }

    // Accessors
    public function getJenisLabelAttribute()
    {
        return match ($this->jenis) {
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'kontrak' => 'Kontrak',
            'magang' => 'Magang',
            'freelance' => 'Freelance',
            default => $this->jenis,
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'aktif' => 'bg-success',
            'ditutup' => 'bg-secondary',
            'draft' => 'bg-warning text-dark',
            default => 'bg-secondary',
        };
    }

    public function getGajiRangeAttribute()
    {
        if (!$this->gaji_min && !$this->gaji_max) return 'Tidak disebutkan';
        if ($this->gaji_min && !$this->gaji_max) return 'Mulai ' . $this->gaji_min;
        if (!$this->gaji_min && $this->gaji_max) return 'Hingga ' . $this->gaji_max;
        return $this->gaji_min . ' - ' . $this->gaji_max;
    }

    public function getSisaHariAttribute()
    {
        if (!$this->deadline) return null;
        $diff = now()->diffInDays($this->deadline, false);
        return $diff >= 0 ? $diff : 0;
    }
}