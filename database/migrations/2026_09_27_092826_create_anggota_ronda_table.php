<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_ronda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ronda_id')->constrained('jadwal_ronda')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('warga_id')->nullable()->constrained('warga')->onDelete('set null');
            $table->boolean('hadir')->default(false);
            $table->timestamp('absen_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['jadwal_ronda_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_ronda');
    }
};