<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Cultivo;
use Illuminate\Database\Seeder;

class CultivoCatalogoSeeder extends Seeder
{
    public function run(): void
    {
        Cultivo::firstOrCreate(['nombre' => 'Café'], [
            'variedad' => 'Catuaí',
            'ciclo_dias' => 365,
            'unidad_cosecha_default' => 'quintal',
        ]);

        Cultivo::firstOrCreate(['nombre' => 'Maíz Dulce'], [
            'variedad' => 'Híbrido',
            'ciclo_dias' => 90,
            'unidad_cosecha_default' => 'unidad',
        ]);

        Cultivo::firstOrCreate(['nombre' => 'Pepino'], [
            'variedad' => 'Marketmore',
            'ciclo_dias' => 60,
            'unidad_cosecha_default' => 'kg',
        ]);
    }
}
