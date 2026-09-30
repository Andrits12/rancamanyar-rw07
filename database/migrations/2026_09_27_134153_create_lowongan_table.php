<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lowongan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_lowongan', 30)->unique(); // LWK-20260927-001
            $table->string('judul', 200);                   // Posisi: Staff Admin
            $table->string('perusahaan', 150);              // PT ABC
            $table->text('deskripsi');
            $table->text('kualifikasi')->nullable();
            $table->text('tanggung_jawab')->nullable();

            // Kategori
            $table->enum('jenis', ['full_time', 'part_time', 'kontrak', 'magang', 'freelance'])
                  ->default('full_time');
            $table->string('lokasi', 150)->nullable();
            $table->string('gaji_min', 50)->nullable();
            $table->string('gaji_max', 50)->nullable();

            // Kontak
            $table->string('kontak_nama', 100)->nullable();
            $table->string('kontak_hp', 20)->nullable();
            $table->string('kontak_email', 100)->nullable();

            // Tanggal
            $table->date('tanggal_buka');
            $table->date('deadline')->nullable();

            // Status & meta
            $table->enum('status', ['draft', 'aktif', 'ditutup'])->default('aktif');
            $table->boolean('is_pinned')->default(false);
            $table->foreignId('dibuat_oleh')->constrained('users')->onDelete('cascade');
            $table->integer('views')->default(0);
            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->index(['status', 'deadline']);
            $table->index('jenis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lowongan');
    }
};