<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CicloManoObra extends Model
{
    protected $table = 'ciclo_mano_obra';

    protected $fillable = [
        'ciclo_id',
        'concepto',
        'personas',
        'horas',
        'costo_hora',
        'costo_total',
        'fecha',
        'notas',
    ];

    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(CicloSiembra::class, 'ciclo_id');
    }
}
