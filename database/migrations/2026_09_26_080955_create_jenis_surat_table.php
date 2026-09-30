<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_surat', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique(); // SKD, SKCK, SKN, SKU, SKP
            $table->string('nama', 100);
            $table->text('deskripsi')->nullable();
            $table->text('syarat')->nullable(); // dokumen syarat
            $table->longText('template')->nullable(); // template isi surat
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_surat');
    }
};