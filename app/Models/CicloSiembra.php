<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CicloSiembra extends Model
{
    protected $table = 'ciclos_siembra';

    protected $fillable = [
        'lote_id',
        'cultivo_id',
        'fecha_siembra',
        'fecha_cosecha_estimada',
        'fecha_cosecha_real',
        'estado',
        'notas',
        'created_by',
    ];

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function cultivo(): BelongsTo
    {
        return $this->belongsTo(Cultivo::class, 'cultivo_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function insumos(): HasMany
    {
        return $this->hasMany(CicloInsumo::class, 'ciclo_id');
    }

    public function manoObra(): HasMany
    {
        return $this->hasMany(CicloManoObra::class, 'ciclo_id');
    }

    public function otrosCostos(): HasMany
    {
        return $this->hasMany(CicloOtroCosto::class, 'ciclo_id');
    }

    public function cosechas(): HasMany
    {
        return $this->hasMany(Cosecha::class, 'ciclo_id');
    }

    public function fotos(): MorphMany
    {
        return $this->morphMany(Foto::class, 'fotoable');
    }
}
