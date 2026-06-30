<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Cultivo;
use Illuminate\Database\Seeder;

class CultivoCatalogoSeeder extends Seeder
{
    public function run(): void
    {
        Cultivo::firstOrCreate(['nombre' => 'Maíz'], [
            'variedad' => 'NB-6',
            'ciclo_dias' => 120,
            'unidad_cosecha_default' => 'quintal',
        ]);

        Cultivo::firstOrCreate(['nombre' => 'Frijol'], [
            'variedad' => 'Rojo Selva Negra',
            'ciclo_dias' => 90,
            'unidad_cosecha_default' => 'quintal',
        ]);

        Cultivo::firstOrCreate(['nombre' => 'Arroz'], [
            'variedad' => 'INTA Dorado',
            'ciclo_dias' => 150,
            'unidad_cosecha_default' => 'quintal',
        ]);

        Cultivo::firstOrCreate(['nombre' => 'Café'], [
            'variedad' => 'Catuaí',
            'ciclo_dias' => 365,
            'unidad_cosecha_default' => 'quintal',
        ]);

        Cultivo::firstOrCreate(['nombre' => 'Pitahaya'], [
            'variedad' => 'Lisa roja',
            'ciclo_dias' => 240,
            'unidad_cosecha_default' => 'kg',
        ]);
    }
}
