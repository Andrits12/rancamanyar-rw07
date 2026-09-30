<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Surat extends Model
{
    protected $table = 'surat';

    protected $fillable = [
        'kode_surat', 'jenis_surat_id', 'user_id', 'warga_id', 'rt_id',
        'keperluan', 'data_tambahan', 'catatan_pemohon',
        'status',
        'verifikator_rt_id', 'verifikasi_rt_at', 'catatan_rt',
        'penyetuju_rw_id', 'approval_rw_at', 'catatan_rw',
        'file_pdf', 'qr_code', 'tanggal_surat', 'nomor_surat',
        'selesai_at', 'views',
    ];

    protected function casts(): array
    {
        return [
            'data_tambahan' => 'array',
            'verifikasi_rt_at' => 'datetime',
            'approval_rw_at' => 'datetime',
            'tanggal_surat' => 'date',
            'selesai_at' => 'datetime',
        ];
    }

    // ============ RELASI ============
    public function jenisSurat()
    {
        return $this->belongsTo(JenisSurat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function verifikatorRt()
    {
        return $this->belongsTo(User::class, 'verifikator_rt_id');
    }

    public function penyetujuRw()
    {
        return $this->belongsTo(User::class, 'penyetuju_rw_id');
    }

    public function logs()
    {
        return $this->hasMany(SuratLog::class)->orderBy('created_at', 'asc');
    }

    // ============ AUTO KODE ============
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($s) {
            if (empty($s->kode_surat)) {
                $s->kode_surat = static::generateKode();
            }
            if (empty($s->qr_code)) {
                $s->qr_code = strtoupper(Str::random(20));
            }
        });
    }

    protected static function generateKode()
    {
        $tanggal = now()->format('Ymd');
        $prefix = 'SRT-' . $tanggal . '-';

        $last = static::where('kode_surat', 'like', $prefix . '%')
            ->orderBy('kode_surat', 'desc')
            ->first();

        $nomor = $last ? ((int) substr($last->kode_surat, -3)) + 1 : 1;

        return $prefix . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    // ============ ACCESSOR ============
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'diajukan' => 'bg-secondary',
            'verifikasi_rt' => 'bg-info text-dark',
            'ditolak_rt' => 'bg-danger',
            'verifikasi_rw' => 'bg-warning text-dark',
            'ditolak_rw' => 'bg-danger',
            'selesai' => 'bg-success',
            default => 'bg-secondary',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'diajukan' => 'Diajukan',
            'verifikasi_rt' => 'Verifikasi RT',
            'ditolak_rt' => 'Ditolak RT',
            'verifikasi_rw' => 'Verifikasi RW',
            'ditolak_rw' => 'Ditolak RW',
            'selesai' => 'Selesai',
            default => $this->status,
        };
    }
}