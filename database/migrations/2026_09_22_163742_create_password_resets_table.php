<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('channel', ['whatsapp', 'email']);
            $table->string('tujuan', 100);
            $table->string('kode', 6);
            $table->timestamp('expired_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'kode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_resets');
    }
};