<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anggota_ronda', function (Blueprint $table) {
            // Ubah user_id jadi nullable
            $table->foreignId('user_id')->nullable()->change();
            // Tambah nama manual
            $table->string('nama_manual', 100)->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('anggota_ronda', function (Blueprint $table) {
            $table->dropColumn('nama_manual');
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};