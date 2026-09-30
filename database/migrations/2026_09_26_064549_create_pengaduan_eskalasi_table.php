<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaduan_eskalasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengaduan_id')->constrained('pengaduan')->onDelete('cascade');
            $table->foreignId('dari_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('ke_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('alasan');
            $table->enum('status', ['dikirim', 'diterima', 'ditolak'])->default('dikirim');
            $table->text('catatan_rw')->nullable();
            $table->timestamp('diterima_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduan_eskalasi');
    }
};