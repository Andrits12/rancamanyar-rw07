<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RtSeeder extends Seeder
{
    public function run(): void
    {
        $rw = DB::table('rw')->first();

        for ($i = 1; $i <= 15; $i++) {
            DB::table('rt')->insert([
                'rw_id' => $rw->id,
                'nomor_rt' => str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama_rt' => 'RT ' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'ketua_rt_nama' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ 15 RT berhasil di-seed.');
    }
}