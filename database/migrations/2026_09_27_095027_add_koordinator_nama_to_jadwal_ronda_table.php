<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_ronda', function (Blueprint $table) {
            $table->string('koordinator_nama', 100)->nullable()->after('koordinator_id');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_ronda', function (Blueprint $table) {
            $table->dropColumn('koordinator_nama');
        });
    }
};