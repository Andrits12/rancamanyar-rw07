<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kematian', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kematian', 30)->unique(); // KMT-20260930-001

            // Warga meninggal
            $table->foreignId('warga_id')->constrained('warga')->onDelete('cascade');
            $table->foreignId('keluarga_id')->nullable()->constrained('keluarga')->onDelete('set null');
            $table->foreignId('rt_id')->constrained('rt')->onDelete('cascade');

            // Data kematian
            $table->date('tanggal_meninggal');
            $table->time('jam_meninggal')->nullable();
            $table->string('tempat_meninggal', 150)->nullable();
            $table->enum('sebab', [
                'sakit', 'kecelakaan', 'usia_lanjut', 'wabah', 'lainnya'
            ])->default('sakit');
            $table->text('keterangan_sebab')->nullable();

            // Pemakaman
            $table->string('tempat_pemakaman', 150)->nullable();
            $table->date('tanggal_pemakaman')->nullable();

            // Akta
            $table->string('no_akta_kematian', 50)->nullable();
            $table->date('tanggal_akta')->nullable();
            $table->string('dokumen_akta')->nullable();

            // Meta
            $table->text('keterangan')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['tanggal_meninggal', 'rt_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kematian');
    }
};