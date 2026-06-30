<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 255);
            $table->decimal('area_manzanas', 10, 4);
            $table->string('tipo_suelo', 100)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
