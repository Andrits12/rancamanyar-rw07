<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';

    protected $fillable = [
        'tipe',
        'judul',
        'slug',
        'kategori',
        'ringkasan',
        'konten',
        'gambar',
        'lampiran',
        'status',
        'is_pinned',
        'penulis_id',
        'published_at',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    // ============ RELASI ============
    public function penulis()
    {
        return $this->belongsTo(User::class, 'penulis_id');
    }

    // ============ SCOPES ============
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopePengumuman($query)
    {
        return $query->where('tipe', 'pengumuman');
    }

    public function scopeBerita($query)
    {
        return $query->where('tipe', 'berita');
    }

    // ============ AUTO SLUG ============
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($p) {
            if (empty($p->slug)) {
                $p->slug = static::generateSlug($p->judul);
            }
        });

        static::updating(function ($p) {
            if ($p->isDirty('judul')) {
                $p->slug = static::generateSlug($p->judul, $p->id);
            }
        });
    }

    protected static function generateSlug($judul, $exceptId = null)
    {
        $slug = Str::slug($judul);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->exists()
        ) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }

    // ============ ACCESSOR ============
    public function getGambarUrlAttribute()
    {
        if (!$this->gambar) return null;
        return asset('storage/' . $this->gambar);
    }
}