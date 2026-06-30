<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lote;

use App\Models\Lote;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LoteController
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        $perPage = min(max($perPage, 5), 50);

        $page = Lote::with(['ciclos.cultivo', 'usuariosAsignados'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->through(fn ($l) => [
                'id' => $l->id,
                'codigo' => $l->codigo,
                'nombre' => $l->nombre,
                'area_manzanas' => $l->area_manzanas,
                'tipo_suelo' => $l->tipo_suelo,
                'lat' => (float) $l->lat,
                'lng' => (float) $l->lng,
                'poligono' => $l->poligono,
                'activo' => $l->activo,
                'descripcion' => $l->descripcion,
                'ciclo_actual' => $l->ciclos->sortByDesc('fecha_siembra')->first(),
            ]);

        return Inertia::render('Lotes/Index', [
            'lotes' => $page->items(),
            'pagination' => [
                'current_page' => $page->currentPage(),
                'last_page' => $page->lastPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Lotes/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:20|unique:lotes,codigo',
            'nombre' => 'required|string|max:255',
            'area_manzanas' => 'required|numeric|min:0.01|max:99999',
            'tipo_suelo' => 'nullable|string|max:100',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'poligono' => 'nullable|json',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $data['created_by'] = auth()->id();
        Lote::create($data);

        return redirect()->route('lotes.index')->with('success', 'Lote creado correctamente.');
    }

    public function edit(Lote $lote)
    {
        return Inertia::render('Lotes/Edit', [
            'lote' => [
                'id' => $lote->id,
                'codigo' => $lote->codigo,
                'nombre' => $lote->nombre,
                'area_manzanas' => (float) $lote->area_manzanas,
                'tipo_suelo' => $lote->tipo_suelo,
                'lat' => (float) $lote->lat,
                'lng' => (float) $lote->lng,
                'poligono' => $lote->poligono,
                'descripcion' => $lote->descripcion,
                'activo' => $lote->activo,
            ],
        ]);
    }

    public function update(Request $request, Lote $lote)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:20|unique:lotes,codigo,' . $lote->id,
            'nombre' => 'required|string|max:255',
            'area_manzanas' => 'required|numeric|min:0.01|max:99999',
            'tipo_suelo' => 'nullable|string|max:100',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'poligono' => 'nullable|json',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $lote->update($data);

        return redirect()->route('lotes.index')->with('success', 'Lote actualizado correctamente.');
    }
}
