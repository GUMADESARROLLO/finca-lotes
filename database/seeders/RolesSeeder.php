<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $gestionarUsuarios = Permission::firstOrCreate(['name' => 'gestionar usuarios']);
        $gestionarCatalogos = Permission::firstOrCreate(['name' => 'gestionar catalogos']);
        $gestionarLotes = Permission::firstOrCreate(['name' => 'gestionar lotes']);
        $verReportesGlobales = Permission::firstOrCreate(['name' => 'ver reportes globales']);
        $verLotesAsignados = Permission::firstOrCreate(['name' => 'ver lotes asignados']);
        $editarLotesAsignados = Permission::firstOrCreate(['name' => 'editar lotes asignados']);
        $registrarCosechas = Permission::firstOrCreate(['name' => 'registrar cosechas']);
        $verReportesScope = Permission::firstOrCreate(['name' => 'ver reportes scope']);
        $subirFotos = Permission::firstOrCreate(['name' => 'subir fotos']);

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $supervisor = Role::firstOrCreate(['name' => 'supervisor']);
        $operador = Role::firstOrCreate(['name' => 'operador']);

        $admin->syncPermissions([
            $gestionarUsuarios,
            $gestionarCatalogos,
            $gestionarLotes,
            $verReportesGlobales,
        ]);

        $supervisor->syncPermissions([
            $verLotesAsignados,
            $editarLotesAsignados,
            $registrarCosechas,
            $verReportesScope,
        ]);

        $operador->syncPermissions([
            $verLotesAsignados,
            $registrarCosechas,
            $subirFotos,
        ]);
    }
}
