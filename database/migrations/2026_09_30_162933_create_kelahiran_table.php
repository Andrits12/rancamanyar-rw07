<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelahiran', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kelahiran', 30)->unique(); // KLR-20260930-001

            // Data bayi
            $table->string('nama_bayi', 100);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir');
            $table->time('jam_lahir')->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->decimal('berat_lahir', 5, 2)->nullable(); // kg
            $table->decimal('panjang_lahir', 5, 2)->nullable(); // cm
            $table->enum('kondisi_lahir', ['normal', 'prematur', 'cacat', 'lainnya'])->default('normal');

            // Orang tua
            $table->string('nama_ayah', 100);
            $table->string('nik_ayah', 16)->nullable();
            $table->string('nama_ibu', 100);
            $table->string('nik_ibu', 16)->nullable();

            // Relasi
            $table->foreignId('keluarga_id')->nullable()->constrained('keluarga')->onDelete('set null');
            $table->foreignId('warga_id')->nullable()->constrained('warga')->onDelete('set null')
                  ->comment('Jika bayi sudah didaftarkan sebagai warga');
            $table->foreignId('rt_id')->constrained('rt')->onDelete('cascade');

            // Akta
            $table->string('no_akta_kelahiran', 50)->nullable();
            $table->date('tanggal_akta')->nullable();
            $table->string('dokumen_akta')->nullable();

            // Meta
            $table->text('keterangan')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['tanggal_lahir', 'rt_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelahiran');
    }
};