<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Lote extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'area_manzanas',
        'tipo_suelo',
        'lat',
        'lng',
        'descripcion',
        'activo',
        'created_by',
    ];

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function ciclos(): HasMany
    {
        return $this->hasMany(CicloSiembra::class, 'lote_id');
    }

    public function fotos(): MorphMany
    {
        return $this->morphMany(Foto::class, 'fotoable');
    }

    public function usuariosAsignados()
    {
        return $this->belongsToMany(User::class, 'lote_user')
            ->withPivot('rol_en_lote')
            ->withTimestamps();
    }

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}
