<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciclo_mano_obra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciclo_id')->constrained('ciclos_siembra');
            $table->string('concepto', 255);
            $table->unsignedInteger('personas');
            $table->decimal('horas', 8, 2);
            $table->decimal('costo_hora', 12, 2);
            $table->decimal('costo_total', 12, 2);
            $table->date('fecha')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciclo_mano_obra');
    }
};
