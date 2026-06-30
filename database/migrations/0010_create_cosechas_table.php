<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cosechas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciclo_id')->constrained('ciclos_siembra');
            $table->date('fecha');
            $table->decimal('cantidad', 12, 3);
            $table->string('unidad', 20);
            $table->string('calidad', 1)->nullable();
            $table->decimal('perdidas_cantidad', 12, 3)->nullable();
            $table->string('perdidas_unidad', 20)->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('registrado_por')->constrained('users');
            $table->timestamps();
            $table->index(['ciclo_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cosechas');
    }
};
