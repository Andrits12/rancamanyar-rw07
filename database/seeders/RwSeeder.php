<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RwSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('rw')->insert([
            'kode_rw' => '007',
            'nama_rw' => 'RW 07',
            'desa' => 'Wancimekar',
            'kecamatan' => 'Kotabaru',
            'kabupaten' => 'Karawang',
            'provinsi' => 'Jawa Barat',
            'ketua_rw_nama' => null,
            'alamat_sekretariat' => 'Perumahan Rancamanyar RW 07',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ RW 07 Wancimekar berhasil di-seed.');
    }
}