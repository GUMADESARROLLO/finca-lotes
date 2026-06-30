<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciclos_siembra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes');
            $table->foreignId('cultivo_id')->constrained('cultivos');
            $table->date('fecha_siembra');
            $table->date('fecha_cosecha_estimada');
            $table->date('fecha_cosecha_real')->nullable();
            $table->string('estado', 20)->default('planificado');
            $table->text('notas')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->index(['lote_id', 'estado']);
            $table->index('fecha_cosecha_estimada');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciclos_siembra');
    }
};
