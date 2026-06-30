<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CicloSiembra;
use App\Models\Cosecha;
use App\Models\Insumo;
use App\Models\Lote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::findByName('admin');
        $supervisorRole = Role::findByName('supervisor');
        $operadorRole = Role::findByName('operador');

        $admin = User::factory()->create([
            'name' => 'Admin Finca',
            'email' => 'admin@finca.test',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole($adminRole);

        $supervisor = User::factory()->create([
            'name' => 'Carlos Supervisor',
            'email' => 'supervisor@finca.test',
            'password' => bcrypt('password'),
        ]);
        $supervisor->assignRole($supervisorRole);

        $operador = User::factory()->create([
            'name' => 'Juan Operador',
            'email' => 'operador@finca.test',
            'password' => bcrypt('password'),
        ]);
        $operador->assignRole($operadorRole);

        $nombres = [
            'El Rosario', 'San Isidro', 'La Esperanza', 'Bella Vista', 'Los Pinos',
            'El Encanto', 'San José', 'Las Flores', 'El Porvenir', 'Santa Fe',
            'La Fortuna', 'El Carmen', 'San Miguel', 'Las Brisas', 'Monte Verde',
            'El Triunfo', 'Santa Rosa', 'Los Angeles', 'La Montaña', 'San Luis',
            'El Diamante', 'La Pradera', 'San Rafael', 'El Roble', 'La Ceiba',
            'Santa Clara', 'El Valle', 'Las Palmeras', 'San Antonio', 'La Gloria',
        ];

        $suelos = ['Franco arenoso', 'Arcilloso', 'Limoso', 'Franco', 'Arenoso'];
        $cultivosSeeder = [1, 2, 3, 4, 5];
        $estados = ['activo', 'cosechado', 'planificado'];

        $hoy = Carbon::today();
        $lotes = [];
        $usuarios = [$admin->id, $supervisor->id, $operador->id];

        $centerLat = 11.9848;
        $centerLng = -86.3088;
        $cols = 6;

        for ($i = 0; $i < 30; $i++) {
            $row = intdiv($i, $cols);
            $col = $i % $cols;

            $lat = $centerLat + ($row - 2) * 0.015 + (random_int(-3, 3) / 1000);
            $lng = $centerLng + ($col - 2.5) * 0.015 + (random_int(-3, 3) / 1000);

            $lote = Lote::create([
                'codigo' => 'L-' . str_pad((string)($i + 1), 3, '0', STR_PAD_LEFT),
                'nombre' => 'Lote ' . $nombres[$i],
                'area_manzanas' => round(random_int(15, 350) / 10, 1),
                'tipo_suelo' => $suelos[array_rand($suelos)],
                'lat' => $lat,
                'lng' => $lng,
                'descripcion' => 'Lote ubicado en sector ' . ($col < 3 ? 'norte' : 'sur') . ', parcela ' . ($row + 1),
                'activo' => $i < 28,
                'created_by' => $usuarios[array_rand($usuarios)],
            ]);

            $lotes[] = $lote;

            if ($i < 20) {
                $supervisorId = $usuarios[array_rand(array_slice($usuarios, 1))];
                $lote->usuariosAsignados()->attach($supervisorId, ['rol_en_lote' => 'supervisor']);
                if ($i % 3 === 0) {
                    $lote->usuariosAsignados()->attach($operador->id, ['rol_en_lote' => 'operador']);
                }
            }
        }

        $insumos = [
            Insumo::create(['nombre' => 'Urea', 'tipo' => 'fertilizante', 'unidad' => 'kg']),
            Insumo::create(['nombre' => 'Fertilizante 15-15-15', 'tipo' => 'fertilizante', 'unidad' => 'kg']),
            Insumo::create(['nombre' => 'Glifosato', 'tipo' => 'herbicida', 'unidad' => 'litro']),
            Insumo::create(['nombre' => 'Semilla de maíz NB-6', 'tipo' => 'semilla', 'unidad' => 'kg']),
            Insumo::create(['nombre' => 'Semilla de frijol', 'tipo' => 'semilla', 'unidad' => 'kg']),
        ];

        for ($i = 0; $i < 15; $i++) {
            $cultivoId = $cultivosSeeder[array_rand($cultivosSeeder)];
            $cultivo = \App\Models\Cultivo::find($cultivoId);
            $diasPasados = random_int(10, 150);
            $estado = $diasPasados > 120 ? 'cosechado' : ($diasPasados < 40 ? 'activo' : (random_int(0, 1) ? 'activo' : 'cosechado'));

            $ciclo = CicloSiembra::create([
                'lote_id' => $lotes[$i]->id,
                'cultivo_id' => $cultivoId,
                'fecha_siembra' => $hoy->copy()->subDays($diasPasados),
                'fecha_cosecha_estimada' => $hoy->copy()->subDays($diasPasados)->addDays($cultivo->ciclo_dias),
                'fecha_cosecha_real' => $estado === 'cosechado' ? $hoy->copy()->subDays(random_int(1, 10)) : null,
                'estado' => $estado,
                'notas' => 'Ciclo de ' . $cultivo->nombre . ' - ' . ($i % 2 === 0 ? 'invierno' : 'verano'),
                'created_by' => $admin->id,
            ]);

            if ($estado === 'cosechado') {
                Cosecha::create([
                    'ciclo_id' => $ciclo->id,
                    'fecha' => $hoy->copy()->subDays(random_int(1, 8)),
                    'cantidad' => round(random_int(150, 600) / 10, 1),
                    'unidad' => $cultivo->unidad_cosecha_default,
                    'calidad' => ['A', 'B', 'C'][array_rand(['A', 'B', 'C'])],
                    'registrado_por' => $supervisor->id,
                ]);
            }

            $ciclo->insumos()->create([
                'insumo_id' => $insumos[array_rand($insumos)]->id,
                'cantidad' => round(random_int(10, 300) / 10, 1),
                'costo_unitario' => round(random_int(500, 5000) / 10, 2),
                'costo_total' => 0,
                'fecha' => $hoy->copy()->subDays($diasPasados - random_int(1, 5)),
            ]);
        }

        // Agregar 3 ciclos planificados para lotes sin ciclo
        for ($i = 15; $i < 18; $i++) {
            $cultivoId = $cultivosSeeder[array_rand($cultivosSeeder)];
            $cultivo = \App\Models\Cultivo::find($cultivoId);
            CicloSiembra::create([
                'lote_id' => $lotes[$i]->id,
                'cultivo_id' => $cultivoId,
                'fecha_siembra' => $hoy->copy()->addDays(random_int(5, 30)),
                'fecha_cosecha_estimada' => $hoy->copy()->addDays($cultivo->ciclo_dias),
                'estado' => 'planificado',
                'notas' => 'Ciclo planificado de ' . $cultivo->nombre,
                'created_by' => $admin->id,
            ]);
        }
    }
}
