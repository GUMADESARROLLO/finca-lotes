<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lote_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes');
            $table->foreignId('user_id')->constrained('users');
            $table->string('rol_en_lote', 20);
            $table->timestamps();
            $table->unique(['lote_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lote_user');
    }
};
