<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PeminjamanAset extends Model
{
    protected $table = 'peminjaman_aset';

    protected $fillable = [
        'kode_pinjam', 'aset_id', 'user_id', 'warga_id', 'rt_id',
        'jumlah', 'keperluan', 'tanggal_pinjam', 'tanggal_rencana_kembali',
        'tanggal_kembali_aktual', 'status',
        'disetujui_oleh', 'disetujui_at', 'catatan_approval',
        'kondisi_kembali', 'catatan_kembali', 'foto_kembali', 'catatan_peminjam',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'tanggal_rencana_kembali' => 'date',
            'tanggal_kembali_aktual' => 'date',
            'disetujui_at' => 'datetime',
        ];
    }

    public function aset() { return $this->belongsTo(Aset::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function warga() { return $this->belongsTo(Warga::class); }
    public function rt() { return $this->belongsTo(Rt::class); }
    public function penyetuju() { return $this->belongsTo(User::class, 'disetujui_oleh'); }

    // ============ AUTO KODE ============
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($p) {
            if (empty($p->kode_pinjam)) {
                $p->kode_pinjam = static::generateKode();
            }
        });
    }

    protected static function generateKode()
    {
        $tanggal = now()->format('Ymd');
        $prefix = 'PJM-' . $tanggal . '-';

        $last = static::where('kode_pinjam', 'like', $prefix . '%')
            ->orderBy('kode_pinjam', 'desc')
            ->first();

        $nomor = $last ? ((int) substr($last->kode_pinjam, -3)) + 1 : 1;

        return $prefix . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    // ============ ACCESSOR ============
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'diajukan' => 'bg-secondary',
            'disetujui' => 'bg-info text-dark',
            'ditolak' => 'bg-danger',
            'dipinjam' => 'bg-primary',
            'dikembalikan' => 'bg-success',
            'terlambat' => 'bg-warning text-dark',
            default => 'bg-secondary',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'diajukan' => 'Diajukan',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'dipinjam' => 'Dipinjam',
            'dikembalikan' => 'Dikembalikan',
            'terlambat' => 'Terlambat',
            default => $this->status,
        };
    }

    public function getTerlambatAttribute()
    {
        if (!$this->tanggal_rencana_kembali) return false;
        if (in_array($this->status, ['dikembalikan', 'ditolak'])) return false;
        return now()->gt($this->tanggal_rencana_kembali);
    }
}