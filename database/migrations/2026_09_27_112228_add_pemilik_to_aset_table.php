<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aset', function (Blueprint $table) {
            // Pemilik aset: 'rw' atau 'rt'
            $table->enum('pemilik', ['rw', 'rt'])->default('rw')->after('kode_aset');
            
            // Kalau pemilik = rt, rt_id wajib diisi
            $table->foreignId('rt_id')->nullable()->after('pemilik')
                  ->constrained('rt')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('aset', function (Blueprint $table) {
            $table->dropForeign(['rt_id']);
            $table->dropColumn(['pemilik', 'rt_id']);
        });
    }
};