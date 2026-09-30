<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tiket', 20)->unique(); // contoh: PGD-20260926-001
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('rt_id')->constrained('rt')->onDelete('cascade');
            $table->foreignId('warga_id')->nullable()->constrained('warga')->onDelete('set null');

            $table->enum('kategori', [
                'infrastruktur', 'keamanan', 'kebersihan', 'kesehatan', 'sosial', 'lainnya'
            ])->default('lainnya');

            $table->string('judul', 200);
            $table->text('deskripsi');
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi'])->default('sedang');

            // Lokasi
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('alamat_lokasi')->nullable();

            // Status
            $table->enum('status', ['baru', 'diproses', 'selesai', 'ditolak'])->default('baru');
            $table->timestamp('ditangani_at')->nullable();
            $table->foreignId('ditangani_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('selesai_at')->nullable();
            $table->text('catatan_penyelesaian')->nullable();

            $table->timestamps();

            $table->index(['status', 'prioritas']);
            $table->index(['rt_id', 'status']);
            $table->index('kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};