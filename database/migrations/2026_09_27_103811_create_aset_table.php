<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset', 30)->unique();   // AST-2026-001
            $table->string('nama', 150);
            $table->enum('kategori', [
                'tenda', 'kursi', 'meja', 'elektronik',
                'alat_kerja', 'perlengkapan', 'lainnya'
            ])->default('lainnya');
            $table->text('deskripsi')->nullable();

            // Jumlah & satuan
            $table->integer('jumlah_total')->default(1);
            $table->string('satuan', 30)->default('unit');  // unit, buah, set

            // Kondisi
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'])
                  ->default('baik');

            // Info
            $table->string('lokasi_penyimpanan', 150)->nullable();
            $table->date('tanggal_perolehan')->nullable();
            $table->decimal('harga_perolehan', 15, 2)->nullable();
            $table->string('sumber_perolehan', 100)->nullable(); // Beli, Sumbangan, Bantuan
            $table->string('foto')->nullable();

            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->index(['kategori', 'kondisi']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset');
    }
};