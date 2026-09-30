<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lamaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode_lamaran', 30)->unique(); // LMR-20260927-001
            $table->foreignId('lowongan_id')->constrained('lowongan')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('warga_id')->nullable()->constrained('warga')->onDelete('set null');
            $table->foreignId('rt_id')->nullable()->constrained('rt')->onDelete('set null');

            // Dokumen
            $table->string('cv')->nullable();
            $table->string('surat_lamaran')->nullable();
            $table->string('portfolio')->nullable();

            // Info pelamar
            $table->text('pengalaman')->nullable();
            $table->text('motivasi')->nullable();
            $table->string('no_hp_pelamar', 20)->nullable();
            $table->string('email_pelamar', 100)->nullable();

            // Status alur
            $table->enum('status', [
                'diajukan',         // baru masuk
                'diverifikasi_rt',  // diverifikasi RT
                'diteruskan',       // diteruskan ke perusahaan
                'interview',        // sedang interview
                'diterima',         // lolos
                'ditolak',          // gagal
            ])->default('diajukan');

            // Verifikasi RT
            $table->foreignId('verifikator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verifikasi_at')->nullable();
            $table->text('catatan_verifikasi')->nullable();
            $table->boolean('is_direkomendasikan')->default(false);

            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->unique(['lowongan_id', 'user_id']); // 1 warga 1 lamaran per lowongan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lamaran');
    }
};