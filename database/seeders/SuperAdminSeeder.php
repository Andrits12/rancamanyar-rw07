<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['no_hp' => '081234567890'],
            [
                'nik' => '3215010101010001',
                'name' => 'Super Admin',
                'email' => 'admin@rw07.test',
                'password' => Hash::make('Admin123!'),
                'rt_id' => null, // super admin tidak terikat RT
                'status_kependudukan' => 'warga',
                'status_akun' => 'aktif',
                'must_change_password' => false,
                'verified_at' => now(),
            ]
        );

        $user->assignRole('super_admin');

        $this->command->info('✅ Super Admin berhasil dibuat.');
        $this->command->info('   No HP    : 081234567890');
        $this->command->info('   Password : Admin123!');
    }
}