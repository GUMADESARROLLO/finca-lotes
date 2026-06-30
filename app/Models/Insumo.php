<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    protected $fillable = [
        'nombre',
        'tipo',
        'unidad',
    ];
}
