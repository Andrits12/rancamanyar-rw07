<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisSuratSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode' => 'SKD',
                'nama' => 'Surat Keterangan Domisili',
                'deskripsi' => 'Surat keterangan tempat tinggal/domisili warga',
                'syarat' => "1. Fotokopi KTP\n2. Fotokopi KK\n3. Surat pengantar RT",
                'urutan' => 1,
            ],
            [
                'kode' => 'SKCK',
                'nama' => 'Surat Pengantar SKCK',
                'deskripsi' => 'Surat pengantar untuk membuat SKCK di Polres',
                'syarat' => "1. Fotokopi KTP\n2. Fotokopi KK\n3. Pas foto 3x4 (2 lembar)",
                'urutan' => 2,
            ],
            [
                'kode' => 'SPN',
                'nama' => 'Surat Pengantar Nikah',
                'deskripsi' => 'Surat pengantar untuk keperluan pernikahan',
                'syarat' => "1. Fotokopi KTP\n2. Fotokopi KK\n3. Fotokopi Akta Lahir\n4. Pas foto 2x3 dan 4x6",
                'urutan' => 3,
            ],
            [
                'kode' => 'SKU',
                'nama' => 'Surat Keterangan Usaha',
                'deskripsi' => 'Surat keterangan untuk usaha/UMKM',
                'syarat' => "1. Fotokopi KTP\n2. Fotokopi KK\n3. Foto tempat usaha",
                'urutan' => 4,
            ],
            [
                'kode' => 'SKP',
                'nama' => 'Surat Keterangan Pindah',
                'deskripsi' => 'Surat keterangan pindah domisili',
                'syarat' => "1. Fotokopi KTP\n2. Fotokopi KK\n3. Alamat tujuan pindah",
                'urutan' => 5,
            ],
            [
                'kode' => 'SKTM',
                'nama' => 'Surat Keterangan Tidak Mampu',
                'deskripsi' => 'Surat keterangan untuk keperluan beasiswa/bantuan',
                'syarat' => "1. Fotokopi KTP\n2. Fotokopi KK\n3. Surat pengantar RT",
                'urutan' => 6,
            ],
            [
                'kode' => 'SPU',
                'nama' => 'Surat Pengantar Umum',
                'deskripsi' => 'Surat pengantar untuk keperluan umum lainnya',
                'syarat' => "1. Fotokopi KTP\n2. Jelaskan keperluan",
                'urutan' => 7,
            ],
        ];

        foreach ($data as $d) {
            DB::table('jenis_surat')->updateOrInsert(
                ['kode' => $d['kode']],
                array_merge($d, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✅ 7 jenis surat berhasil di-seed.');
    }
}