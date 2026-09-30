<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warga', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->unique();
            $table->string('nama', 100);
            $table->foreignId('keluarga_id')->constrained('keluarga')->onDelete('cascade');
            $table->foreignId('rt_id')->constrained('rt')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')
                  ->comment('Jika warga ini punya akun login');

            // Data pribadi
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tgl_lahir');
            $table->string('agama', 20)->nullable();
            $table->string('pendidikan', 50)->nullable(); // SD, SMP, SMA, S1, dll
            $table->string('pekerjaan', 100)->nullable();
            $table->string('status_kawin', 30)->nullable(); // Belum Kawin, Kawin, Cerai Hidup, Cerai Mati

            // Status dalam keluarga
            $table->enum('status_keluarga', [
                'kepala_keluarga', 'istri', 'anak', 'menantu',
                'cucu', 'orang_tua', 'mertua', 'famili_lain', 'lainnya'
            ]);

            // Kewarganegaraan & dokumen
            $table->string('kewarganegaraan', 30)->default('WNI');
            $table->string('golongan_darah', 5)->nullable();
            $table->string('no_akta_lahir', 50)->nullable();

            // Status hidup
            $table->enum('status_hidup', ['hidup', 'meninggal', 'pindah'])->default('hidup');
            $table->date('tgl_meninggal')->nullable();
            $table->date('tgl_pindah')->nullable();
            $table->text('keterangan')->nullable();

            // Foto
            $table->string('foto')->nullable();

            $table->timestamps();

            $table->index(['rt_id', 'status_hidup']);
            $table->index(['keluarga_id', 'status_keluarga']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warga');
    }
};