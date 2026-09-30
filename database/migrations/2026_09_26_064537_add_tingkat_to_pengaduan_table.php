<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->enum('tingkat', ['rt', 'rw'])->default('rt')->after('kategori');
            $table->enum('status_eskalasi', ['tidak', 'dieskalasi', 'diterima_rw', 'ditolak_rw'])
                  ->default('tidak')->after('status');
            $table->foreignId('pengaduan_asal_id')->nullable()->after('status_eskalasi')
                  ->constrained('pengaduan')->onDelete('set null')
                  ->comment('ID pengaduan RT yang dieskalasi ke RW ini');
        });
    }

    public function down(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->dropForeign(['pengaduan_asal_id']);
            $table->dropColumn(['tingkat', 'status_eskalasi', 'pengaduan_asal_id']);
        });
    }
};