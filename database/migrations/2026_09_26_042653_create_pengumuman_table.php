<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['pengumuman', 'berita'])->default('pengumuman');
            $table->string('judul', 200);
            $table->string('slug', 220)->unique();
            $table->string('kategori', 50)->default('umum');
            $table->text('ringkasan')->nullable();
            $table->longText('konten');

            // Media
            $table->string('gambar')->nullable();
            $table->string('lampiran')->nullable();

            // Status
            $table->enum('status', ['draft', 'published', 'arsip'])->default('draft');
            $table->boolean('is_pinned')->default(false);

            // Meta
            $table->foreignId('penulis_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('published_at')->nullable();
            $table->integer('views')->default(0);

            $table->timestamps();

            $table->index(['tipe', 'status', 'published_at']);
            $table->index('is_pinned');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};