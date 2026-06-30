<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Lote\LoteController;
use App\Http\Controllers\Ciclo\CicloController;
use App\Http\Controllers\Costo\CostoController;
use App\Http\Controllers\Cosecha\CosechaController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login')
);

Route::get('/dashboard', fn () => Inertia\Inertia::render('Dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/lotes', [LoteController::class, 'index'])->name('lotes.index');
    Route::get('/lotes/crear', [LoteController::class, 'create'])->name('lotes.create');
    Route::post('/lotes', [LoteController::class, 'store'])->name('lotes.store');
    Route::get('/lotes/{lote}/editar', [LoteController::class, 'edit'])->name('lotes.edit');
    Route::put('/lotes/{lote}', [LoteController::class, 'update'])->name('lotes.update');

    Route::get('/ciclos', [CicloController::class, 'index'])->name('ciclos.index');
    Route::get('/ciclos/crear', [CicloController::class, 'create'])->name('ciclos.create');
    Route::post('/ciclos', [CicloController::class, 'store'])->name('ciclos.store');
    Route::get('/ciclos/{ciclo}', [CicloController::class, 'show'])->name('ciclos.show');
    Route::post('/ciclos/{ciclo}/insumos', [CicloController::class, 'addInsumo'])->name('ciclos.insumos.store');
    Route::post('/ciclos/{ciclo}/mano-obra', [CicloController::class, 'addManoObra'])->name('ciclos.mano-obra.store');
    Route::post('/ciclos/{ciclo}/otros-costos', [CicloController::class, 'addOtroCosto'])->name('ciclos.otros-costos.store');
    Route::get('/insumos/lista', [\App\Http\Controllers\Insumo\InsumoController::class, 'lista'])->name('insumos.lista');
    Route::get('/costos', [CostoController::class, 'index'])->name('costos.index');
    Route::get('/cosechas', [CosechaController::class, 'index'])->name('cosechas.index');
});

require __DIR__.'/auth.php';
