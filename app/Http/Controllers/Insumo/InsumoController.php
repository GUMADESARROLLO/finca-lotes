<?php

declare(strict_types=1);

namespace App\Http\Controllers\Insumo;

use App\Models\Insumo;

class InsumoController
{
    public function lista()
    {
        return response()->json(Insumo::orderBy('nombre')->get(['id', 'nombre', 'tipo', 'unidad']));
    }
}
