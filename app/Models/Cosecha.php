<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cosecha extends Model
{
    protected $fillable = [
        'ciclo_id',
        'fecha',
        'cantidad',
        'unidad',
        'calidad',
        'perdidas_cantidad',
        'perdidas_unidad',
        'notas',
        'registrado_por',
    ];

    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(CicloSiembra::class, 'ciclo_id');
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
