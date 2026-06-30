<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insumos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('tipo', 20);
            $table->string('unidad', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insumos');
    }
};
