<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'nik',
        'no_hp',
        'name',
        'email',
        'password',
        'foto',
        'rt_id',
        'alamat',
        'no_kk',
        'status_kependudukan',
        'jenis_kelamin',
        'tgl_lahir',
        'agama',
        'pekerjaan',
        'status_kawin',
        'status_keluarga',
        'jabatan',
        'periode_jabatan_mulai',
        'periode_jabatan_selesai',
        'status_akun',
        'must_change_password',
        'verified_at',
        'verified_by',
        'last_password_change',
        'last_login_at',
        'last_login_ip',
        'warga_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tgl_lahir' => 'date',
            'periode_jabatan_mulai' => 'date',
            'periode_jabatan_selesai' => 'date',
            'verified_at' => 'datetime',
            'last_password_change' => 'datetime',
            'last_login_at' => 'datetime',
            'must_change_password' => 'boolean',
        ];
    }

    // ============ RELASI ============
    public function rt()
    {
        return $this->belongsTo(\App\Models\Rt::class);
    }

    public function warga()
    {
    return $this->belongsTo(\App\Models\Warga::class);
    }

}