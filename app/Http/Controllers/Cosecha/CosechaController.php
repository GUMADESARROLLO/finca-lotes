<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cosecha;

use Inertia\Inertia;

class CosechaController
{
    public function index()
    {
        return Inertia::render('Cosechas/Index');
    }
}
