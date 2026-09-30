<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LokasiPentingSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama' => 'Masjid Al-Ikhlas', 'kategori' => 'masjid', 'latitude' => -6.3289, 'longitude' => 107.3079, 'alamat' => 'Blok A RW 07'],
            ['nama' => 'Pos Ronda RT 01', 'kategori' => 'pos_ronda', 'latitude' => -6.3290, 'longitude' => 107.3080, 'alamat' => 'Depan Blok A'],
            ['nama' => 'Posyandu Melati', 'kategori' => 'posyandu', 'latitude' => -6.3291, 'longitude' => 107.3081, 'alamat' => 'Samping Balai RW'],
        ];

        foreach ($data as $d) {
            DB::table('lokasi_penting')->insert(array_merge($d, [
                'warna' => match ($d['kategori']) {
                    'masjid' => '#f59e0b',
                    'pos_ronda' => '#10b981',
                    'posyandu' => '#ec4899',
                    default => '#667eea',
                },
                'is_public' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✅ 3 lokasi contoh berhasil di-seed.');
    }
}