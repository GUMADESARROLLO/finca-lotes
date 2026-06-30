<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cultivo extends Model
{
    protected $fillable = [
        'nombre',
        'variedad',
        'ciclo_dias',
        'unidad_cosecha_default',
        'activo',
    ];

    public function ciclos(): HasMany
    {
        return $this->hasMany(CicloSiembra::class, 'cultivo_id');
    }

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}
