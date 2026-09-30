<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keluarga', function (Blueprint $table) {
            $table->id();
            $table->string('no_kk', 16)->unique();
            $table->foreignId('rt_id')->constrained('rt')->onDelete('cascade');
            $table->string('kepala_keluarga_nama', 100);
            $table->text('alamat');
            $table->string('status_rumah', 30)->nullable(); // milik sendiri, sewa, kontrak, dll
            $table->integer('jumlah_anggota')->default(0);
            $table->enum('status_keluarga', ['aktif', 'pindah', 'nonaktif'])->default('aktif');
            $table->date('tgl_daftar')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keluarga');
    }
};