<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_ronda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ronda_id')->constrained('jadwal_ronda')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('kondisi', ['aman', 'ada_kejadian', 'perlu_tindak_lanjut'])->default('aman');
            $table->text('catatan');
            $table->string('foto')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_ronda');
    }
};