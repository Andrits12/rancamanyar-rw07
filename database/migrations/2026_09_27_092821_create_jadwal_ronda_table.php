<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_ronda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rt_id')->constrained('rt')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('shift', ['malam_1', 'malam_2', 'subuh'])->default('malam_1');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->foreignId('koordinator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('pos_ronda', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['draft', 'aktif', 'selesai', 'batal'])->default('aktif');
            $table->timestamps();

            $table->index(['tanggal', 'rt_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_ronda');
    }
};