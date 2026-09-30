<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('panic_button', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('rt_id')->nullable()->constrained('rt')->onDelete('set null');
            $table->enum('jenis', ['kebakaran', 'medis', 'kriminal', 'bencana', 'lainnya'])->default('lainnya');
            $table->text('keterangan')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('alamat_lokasi')->nullable();
            $table->string('foto')->nullable();
            $table->enum('status', ['baru', 'ditangani', 'selesai', 'false_alarm'])->default('baru');
            $table->foreignId('ditangani_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('ditangani_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->text('catatan_penanganan')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panic_button');
    }
};