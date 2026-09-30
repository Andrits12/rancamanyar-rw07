<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Identitas
            $table->string('nik', 16)->unique()->after('id');
            $table->string('no_hp', 15)->unique()->after('nik');
            $table->string('foto')->nullable()->after('email');

            // Kependudukan
            $table->foreignId('rt_id')->nullable()->after('foto')
                  ->constrained('rt')->onDelete('set null');
            $table->text('alamat')->nullable();
            $table->string('no_kk', 16)->nullable();
            $table->enum('status_kependudukan', ['warga', 'pendatang', 'kontrak'])
                  ->default('warga');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('agama', 20)->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->string('status_kawin', 30)->nullable();
            $table->enum('status_keluarga', ['kepala', 'istri', 'anak', 'famili', 'lainnya'])
                  ->default('kepala');

            // Jabatan
            $table->string('jabatan', 100)->nullable();
            $table->date('periode_jabatan_mulai')->nullable();
            $table->date('periode_jabatan_selesai')->nullable();

            // Akun
            $table->enum('status_akun', ['pending', 'aktif', 'nonaktif'])->default('aktif');
            $table->boolean('must_change_password')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->after('verified_at');
            $table->timestamp('last_password_change')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['rt_id']);
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'nik', 'no_hp', 'foto', 'rt_id', 'alamat', 'no_kk',
                'status_kependudukan', 'jenis_kelamin', 'tgl_lahir',
                'agama', 'pekerjaan', 'status_kawin', 'status_keluarga',
                'jabatan', 'periode_jabatan_mulai', 'periode_jabatan_selesai',
                'status_akun', 'must_change_password', 'verified_at',
                'verified_by', 'last_password_change', 'last_login_at', 'last_login_ip',
            ]);
        });
    }
};