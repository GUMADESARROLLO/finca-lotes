<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CicloOtroCosto extends Model
{
    protected $table = 'ciclo_otros_costos';

    protected $fillable = [
        'ciclo_id',
        'concepto',
        'monto',
        'fecha',
        'notas',
    ];

    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(CicloSiembra::class, 'ciclo_id');
    }
}
