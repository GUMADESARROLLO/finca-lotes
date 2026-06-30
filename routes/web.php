<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/lotes', function () {
        $perPage = (int) request()->query('per_page', 10);
        $perPage = min(max($perPage, 5), 50);

        $page = \App\Models\Lote::with(['ciclos.cultivo', 'usuariosAsignados'])
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
    })->name('lotes.index');
    Route::get('/ciclos', fn () => Inertia::render('Ciclos/Index'))->name('ciclos.index');
    Route::get('/costos', fn () => Inertia::render('Costos/Index'))->name('costos.index');
    Route::get('/cosechas', fn () => Inertia::render('Cosechas/Index'))->name('cosechas.index');
});

require __DIR__.'/auth.php';
