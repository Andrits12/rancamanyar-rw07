<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat', function (Blueprint $table) {
            $table->id();
            $table->string('kode_surat', 30)->unique(); // SRT-20260926-001
            $table->foreignId('jenis_surat_id')->constrained('jenis_surat')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // pemohon
            $table->foreignId('warga_id')->nullable()->constrained('warga')->onDelete('set null');
            $table->foreignId('rt_id')->constrained('rt')->onDelete('cascade');

            // Data permohonan
            $table->text('keperluan');
            $table->json('data_tambahan')->nullable(); // data spesifik per jenis surat
            $table->text('catatan_pemohon')->nullable();

            // Status workflow
            $table->enum('status', [
                'diajukan',
                'verifikasi_rt',
                'ditolak_rt',
                'verifikasi_rw',
                'ditolak_rw',
                'selesai'
            ])->default('diajukan');

            // Verifikasi RT
            $table->foreignId('verifikator_rt_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verifikasi_rt_at')->nullable();
            $table->text('catatan_rt')->nullable();

            // Approval RW
            $table->foreignId('penyetuju_rw_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approval_rw_at')->nullable();
            $table->text('catatan_rw')->nullable();

            // File
            $table->string('file_pdf')->nullable();
            $table->string('qr_code', 50)->nullable()->unique(); // untuk verifikasi
            $table->timestamp('tanggal_surat')->nullable();
            $table->string('nomor_surat', 50)->nullable(); // nomor resmi surat

            // Tracking
            $table->timestamp('selesai_at')->nullable();
            $table->integer('views')->default(0);

            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['rt_id', 'status']);
            $table->index('jenis_surat_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat');
    }
};