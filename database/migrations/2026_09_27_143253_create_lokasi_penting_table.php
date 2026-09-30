<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi_penting', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->enum('kategori', [
                'rumah_warga',
                'pos_ronda',
                'posyandu',
                'masjid',
                'sekolah',
                'umkm',
                'aset_rw',
                'fasilitas_umum',
                'lainnya'
            ])->default('lainnya');

            $table->text('deskripsi')->nullable();
            $table->text('alamat')->nullable();

            // Koordinat
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // Relasi opsional
            $table->foreignId('rt_id')->nullable()->constrained('rt')->onDelete('set null');
            $table->foreignId('warga_id')->nullable()->constrained('warga')->onDelete('set null');

            // Info
            $table->string('foto')->nullable();
            $table->string('ikon', 50)->default('bi-geo-alt-fill');
            $table->string('warna', 20)->default('#667eea');
            $table->string('kontak', 100)->nullable();

            // Status
            $table->boolean('is_public')->default(true);
            $table->boolean('is_active')->default(true);

            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['kategori', 'is_active']);
            $table->index('rt_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi_penting');
    }
};