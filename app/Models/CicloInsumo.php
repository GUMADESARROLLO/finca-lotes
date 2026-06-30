<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CicloInsumo extends Model
{
    protected $table = 'ciclo_insumos';

    protected $fillable = [
        'ciclo_id',
        'insumo_id',
        'cantidad',
        'costo_unitario',
        'costo_total',
        'fecha',
        'notas',
    ];

    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(CicloSiembra::class, 'ciclo_id');
    }

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }
}
