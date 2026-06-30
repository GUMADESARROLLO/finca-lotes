<?php

declare(strict_types=1);

namespace App\Http\Controllers\Costo;

use Inertia\Inertia;

class CostoController
{
    public function index()
    {
        return Inertia::render('Costos/Index');
    }
}
