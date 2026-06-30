<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ciclo;

use App\Models\CicloInsumo;
use App\Models\CicloManoObra;
use App\Models\CicloOtroCosto;
use App\Models\CicloSiembra;
use App\Models\Cultivo;
use App\Models\Insumo;
use App\Models\Lote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CicloController
{
    private function renderShow(CicloSiembra $ciclo)
    {
        $ciclo->load(['lote', 'cultivo', 'insumos.insumo', 'manoObra', 'otrosCostos', 'cosechas']);

        $totalCostos = $ciclo->insumos->sum('costo_total')
            + $ciclo->manoObra->sum('costo_total')
            + $ciclo->otrosCostos->sum('monto');

        return [
            'id' => $ciclo->id,
            'lote' => $ciclo->lote ? ['id' => $ciclo->lote->id, 'nombre' => $ciclo->lote->nombre, 'codigo' => $ciclo->lote->codigo] : null,
            'cultivo' => $ciclo->cultivo ? ['id' => $ciclo->cultivo->id, 'nombre' => $ciclo->cultivo->nombre, 'ciclo_dias' => $ciclo->cultivo->ciclo_dias] : null,
            'fecha_siembra' => $ciclo->fecha_siembra,
            'fecha_cosecha_estimada' => $ciclo->fecha_cosecha_estimada,
            'fecha_cosecha_real' => $ciclo->fecha_cosecha_real,
            'estado' => $ciclo->estado,
            'notas' => $ciclo->notas,
            'insumos' => $ciclo->insumos->map(fn ($ci) => [
                'id' => $ci->id, 'insumo_id' => $ci->insumo_id, 'insumo_nombre' => $ci->insumo?->nombre,
                'insumo_tipo' => $ci->insumo?->tipo, 'insumo_unidad' => $ci->insumo?->unidad,
                'cantidad' => (float) $ci->cantidad, 'costo_unitario' => (float) $ci->costo_unitario,
                'costo_total' => (float) $ci->costo_total, 'fecha' => $ci->fecha, 'notas' => $ci->notas,
            ]),
            'mano_obra' => $ciclo->manoObra->map(fn ($m) => [
                'id' => $m->id, 'concepto' => $m->concepto, 'personas' => $m->personas, 'horas' => (float) $m->horas,
                'costo_hora' => (float) $m->costo_hora, 'costo_total' => (float) $m->costo_total, 'fecha' => $m->fecha, 'notas' => $m->notas,
            ]),
            'otros_costos' => $ciclo->otrosCostos->map(fn ($o) => [
                'id' => $o->id, 'concepto' => $o->concepto, 'monto' => (float) $o->monto, 'fecha' => $o->fecha, 'notas' => $o->notas,
            ]),
            'total_costos' => $totalCostos,
        ];
    }

    public function index(Request $request)
    {
        $ciclos = CicloSiembra::with(['lote', 'cultivo'])
            ->orderByRaw("FIELD(estado, 'activo', 'planificado', 'cosechado')")
            ->orderBy('fecha_cosecha_estimada')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'lote' => $c->lote ? ['id' => $c->lote->id, 'nombre' => $c->lote->nombre] : null,
                'cultivo' => $c->cultivo ? ['id' => $c->cultivo->id, 'nombre' => $c->cultivo->nombre] : null,
                'fecha_siembra' => $c->fecha_siembra,
                'fecha_cosecha_estimada' => $c->fecha_cosecha_estimada,
                'estado' => $c->estado,
                'etapa' => $c->estado === 'planificado' ? 'Planificado'
                    : ($c->estado === 'activo' ? 'En crecimiento' : 'Cosechado'),
                'progreso' => $c->estado === 'cosechado' ? 100
                    : min(round(Carbon::parse($c->fecha_siembra)->diffInDays(now()) / max(1, Carbon::parse($c->fecha_siembra)->diffInDays(Carbon::parse($c->fecha_cosecha_estimada))) * 100), 99),
            ]);

        $destacados = $ciclos->where('estado', 'activo')->take(3)->values();

        return Inertia::render('Ciclos/Index', [
            'ciclos' => $ciclos->values(),
            'destacados' => $destacados,
            'stats' => [
                'total' => $ciclos->count(),
                'activos' => $ciclos->where('estado', 'activo')->count(),
                'planificados' => $ciclos->where('estado', 'planificado')->count(),
                'cosechados' => $ciclos->where('estado', 'cosechado')->count(),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Ciclos/Create', [
            'lotes' => Lote::where('activo', true)->orderBy('nombre')->get(['id', 'nombre', 'codigo']),
            'cultivos' => Cultivo::where('activo', true)->orderBy('nombre')->get(['id', 'nombre', 'ciclo_dias']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'cultivo_id' => 'required|exists:cultivos,id',
            'fecha_siembra' => 'required|date',
            'notas' => 'nullable|string',
        ]);

        $cultivo = Cultivo::findOrFail($data['cultivo_id']);
        $data['fecha_cosecha_estimada'] = Carbon::parse($data['fecha_siembra'])->addDays($cultivo->ciclo_dias);
        $data['estado'] = 'planificado';
        $data['created_by'] = auth()->id();

        CicloSiembra::create($data);

        return redirect()->route('ciclos.index')->with('success', 'Ciclo creado correctamente.');
    }

    public function show(CicloSiembra $ciclo)
    {
        return Inertia::render('Ciclos/Show', [
            'ciclo' => $this->renderShow($ciclo),
            'insumos' => Insumo::orderBy('nombre')->get(['id', 'nombre', 'tipo', 'unidad']),
        ]);
    }

    public function addInsumo(Request $request, CicloSiembra $ciclo)
    {
        $data = $request->validate([
            'insumo_id' => 'required_without:insumo_nombre|exists:insumos,id|nullable',
            'insumo_nombre' => 'required_without:insumo_id|string|max:255|nullable',
            'insumo_tipo' => 'required_with:insumo_nombre|string|max:50',
            'insumo_unidad' => 'required_with:insumo_nombre|string|max:50',
            'cantidad' => 'required|numeric|min:0.001',
            'costo_unitario' => 'required|numeric|min:0',
            'fecha' => 'nullable|date',
            'notas' => 'nullable|string',
        ]);

        if ($data['insumo_nombre']) {
            $insumo = Insumo::create([
                'nombre' => $data['insumo_nombre'], 'tipo' => $data['insumo_tipo'], 'unidad' => $data['insumo_unidad'],
            ]);
            $data['insumo_id'] = $insumo->id;
        }

        $data['ciclo_id'] = $ciclo->id;
        $data['costo_total'] = $data['cantidad'] * $data['costo_unitario'];
        CicloInsumo::create($data);

        return Inertia::render('Ciclos/Show', [
            'ciclo' => $this->renderShow($ciclo),
            'insumos' => Insumo::orderBy('nombre')->get(['id', 'nombre', 'tipo', 'unidad']),
        ]);
    }

    public function addManoObra(Request $request, CicloSiembra $ciclo)
    {
        $data = $request->validate([
            'concepto' => 'required|string|max:255',
            'personas' => 'required|integer|min:1',
            'horas' => 'required|numeric|min:0.1',
            'costo_hora' => 'required|numeric|min:0',
            'fecha' => 'nullable|date',
            'notas' => 'nullable|string',
        ]);

        $data['ciclo_id'] = $ciclo->id;
        $data['costo_total'] = $data['personas'] * $data['horas'] * $data['costo_hora'];
        CicloManoObra::create($data);

        return Inertia::render('Ciclos/Show', [
            'ciclo' => $this->renderShow($ciclo),
            'insumos' => Insumo::orderBy('nombre')->get(['id', 'nombre', 'tipo', 'unidad']),
        ]);
    }

    public function addOtroCosto(Request $request, CicloSiembra $ciclo)
    {
        $data = $request->validate([
            'concepto' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'fecha' => 'nullable|date',
            'notas' => 'nullable|string',
        ]);

        $data['ciclo_id'] = $ciclo->id;
        CicloOtroCosto::create($data);

        return Inertia::render('Ciclos/Show', [
            'ciclo' => $this->renderShow($ciclo),
            'insumos' => Insumo::orderBy('nombre')->get(['id', 'nombre', 'tipo', 'unidad']),
        ]);
    }
}
