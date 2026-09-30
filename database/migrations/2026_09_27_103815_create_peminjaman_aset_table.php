<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_aset', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pinjam', 30)->unique(); // PJM-20260927-001
            $table->foreignId('aset_id')->constrained('aset')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // peminjam
            $table->foreignId('warga_id')->nullable()->constrained('warga')->onDelete('set null');
            $table->foreignId('rt_id')->nullable()->constrained('rt')->onDelete('set null');

            // Detail peminjaman
            $table->integer('jumlah');
            $table->text('keperluan');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_rencana_kembali');
            $table->date('tanggal_kembali_aktual')->nullable();

            // Status
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak', 'dipinjam', 'dikembalikan', 'terlambat'])
                  ->default('diajukan');

            // Approval
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('disetujui_at')->nullable();
            $table->text('catatan_approval')->nullable();

            // Pengembalian
            $table->enum('kondisi_kembali', ['baik', 'rusak_ringan', 'rusak_berat'])->nullable();
            $table->text('catatan_kembali')->nullable();
            $table->string('foto_kembali')->nullable();

            $table->text('catatan_peminjam')->nullable();
            $table->timestamps();

            $table->index(['status', 'tanggal_pinjam']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_aset');
    }
};