<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ============ DAFTAR PERMISSION ============
        $modul = [
            'warga'         => ['view', 'create', 'edit', 'delete', 'export'],
            'keluarga'      => ['view', 'create', 'edit', 'delete', 'export'],
            'pengumuman' => ['view', 'create', 'edit', 'delete', 'publish'],
            'berita'     => ['view', 'create', 'edit', 'delete', 'publish'],
            'kalender'      => ['view', 'create', 'edit', 'delete'],
            'surat'         => ['view', 'create', 'edit', 'delete', 'approve', 'sign', 'export'],
            'keuangan'      => ['view', 'create', 'edit', 'delete', 'approve', 'export'],
            'iuran'         => ['view', 'create', 'edit', 'delete'],
            'pengaduan'     => ['view', 'create', 'edit', 'delete', 'handle', 'export'],
            'ronda'         => ['view', 'create', 'edit', 'delete', 'schedule', 'absen', 'lapor'],
            'panic'         => ['view', 'create', 'handle'],
            'tamu'          => ['view', 'create', 'edit', 'delete'],
            'inventaris'    => ['view', 'create', 'edit', 'delete', 'pinjam', 'approve', 'kembalikan'],
            'umkm'          => ['view', 'create', 'edit', 'delete', 'verifikasi'],
            'lowongan'      => ['view', 'create', 'edit', 'delete', 'lamar', 'verifikasi', 'teruskan'],
            'posyandu'      => ['view', 'create', 'edit', 'delete'],
            'bansos'        => ['view', 'create', 'edit', 'delete'],
            'peta'          => ['view', 'create', 'edit', 'delete'],
            'akta'          => ['view', 'create', 'edit', 'delete'],
            'user'          => ['view', 'create', 'edit', 'delete', 'reset_password'],
            'role'          => ['view', 'create', 'edit', 'delete'],
            'dashboard'     => ['view_rw', 'view_rt', 'view_keuangan', 'view_security'],
        ];

        foreach ($modul as $nama => $aksis) {
            foreach ($aksis as $aksi) {
                Permission::firstOrCreate(['name' => "{$nama}.{$aksi}"]);
            }
        }

        // ============ DAFTAR ROLE ============
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        // Super admin otomatis punya semua permission via Gate::before (lihat langkah 5)
        $superAdmin->givePermissionTo(Permission::all());

        $ketuaRw = Role::firstOrCreate(['name' => 'ketua_rw']);
        $ketuaRw->givePermissionTo(Permission::all());

        $ketuaRt = Role::firstOrCreate(['name' => 'ketua_rt']);
        $ketuaRt->givePermissionTo([
            'dashboard.view_rt',
            'warga.view', 'warga.create', 'warga.edit', 'warga.export',
            'keluarga.view', 'keluarga.create', 'keluarga.edit',
            'pengumuman.view', 'pengumuman.create', 'pengumuman.edit',
            'berita.view', 'kalender.view',
            'surat.view', 'surat.approve',
            'pengaduan.view', 'pengaduan.create', 'pengaduan.handle',
            'ronda.view', 'ronda.schedule',
            'panic.view', 'panic.create', 'panic.handle',
            'tamu.view',
            'inventaris.view', 'inventaris.create', 'inventaris.edit',
            'inventaris.pinjam', 'inventaris.approve', 'inventaris.kembalikan',
            'umkm.view', 'umkm.verifikasi',
            'lowongan.view', 'lowongan.verifikasi', 'lowongan.teruskan',
            'posyandu.view',
            'bansos.view',
            'peta.view', 'peta.create', 'peta.edit',
            'akta.view', 'akta.create', 'akta.edit',
        ]);

        $sekretaris = Role::firstOrCreate(['name' => 'sekretaris']);
        $sekretaris->givePermissionTo([
            'dashboard.view_rw',
            'warga.view', 'warga.create', 'warga.edit', 'warga.export',
            'keluarga.view', 'keluarga.create', 'keluarga.edit', 'keluarga.export',
            'pengumuman.view', 'pengumuman.create', 'pengumuman.edit',
            'berita.view', 'berita.create', 'berita.edit',
            'kalender.view', 'kalender.create', 'kalender.edit',
            'surat.view', 'surat.create', 'surat.edit', 'surat.approve',
            'pengaduan.view',
            'panic.view', 'panic.create',
            'inventaris.view', 'inventaris.create', 'inventaris.edit',
            'umkm.view', 'umkm.verifikasi',
            'lowongan.view',
            'posyandu.view',
            'bansos.view',
            'peta.view', 'peta.create', 'peta.edit',
            'akta.view', 'akta.create', 'akta.edit',
        ]);

        $bendahara = Role::firstOrCreate(['name' => 'bendahara']);
        $bendahara->givePermissionTo([
            'dashboard.view_keuangan',
            'keuangan.view', 'keuangan.create', 'keuangan.edit', 'keuangan.export',
            'iuran.view', 'iuran.create', 'iuran.edit',
            'warga.view',
            'pengumuman.view',
            'inventaris.view',
            'panic.view', 'panic.create',
        ]);

        $sesepuh = Role::firstOrCreate(['name' => 'sesepuh']);
        $sesepuh->givePermissionTo([
            'dashboard.view_rw',
            'warga.view', 'keluarga.view',
            'pengumuman.view', 'berita.view', 'kalender.view',
            'surat.view', 'keuangan.view', 'pengaduan.view',
            'ronda.view', 'tamu.view', 'inventaris.view',
            'umkm.view', 'lowongan.view', 'posyandu.view',
            'bansos.view', 'peta.view', 'akta.view',
            'panic.view', 'panic.create',
        ]);

        $security = Role::firstOrCreate(['name' => 'security']);
        $security->givePermissionTo([
            'dashboard.view_security',
            'tamu.view', 'tamu.create', 'tamu.edit',
            'ronda.view', 'ronda.schedule', 'ronda.absen', 'ronda.lapor',
            'panic.view', 'panic.handle',
            'pengaduan.view', 'pengaduan.create',
        ]);

        $posyandu = Role::firstOrCreate(['name' => 'posyandu']);
        $posyandu->givePermissionTo([
            'warga.view',
            'posyandu.view', 'posyandu.create', 'posyandu.edit',
            'pengumuman.view',
            'peta.view',
            'panic.view', 'panic.create',
        ]);

        $warga = Role::firstOrCreate(['name' => 'warga']);
        $warga->givePermissionTo([
            'pengumuman.view', 'berita.view', 'kalender.view',
            'surat.view', 'surat.create',
            'pengaduan.view', 'pengaduan.create',
            'umkm.view', 'umkm.create',
            'lowongan.view', 'lowongan.lamar',
            'inventaris.view',
            'peta.view',
            'panic.view', 'panic.create',
        ]);

        $this->command->info('✅ Role & Permission berhasil di-seed.');
    }
}