<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciclo_insumos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciclo_id')->constrained('ciclos_siembra');
            $table->foreignId('insumo_id')->constrained('insumos');
            $table->decimal('cantidad', 12, 3);
            $table->decimal('costo_unitario', 12, 2);
            $table->decimal('costo_total', 12, 2);
            $table->date('fecha')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->index('ciclo_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciclo_insumos');
    }
};
