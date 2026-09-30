<?php

use Illuminate\Support\Facades\Route;
use Modules\Pedagogie\Http\Controllers\ClasseController;
use Modules\Pedagogie\Http\Controllers\CycleController;
use Modules\Pedagogie\Http\Controllers\EleveController;
use Modules\Pedagogie\Http\Controllers\ParentController;
use Modules\Pedagogie\Http\Controllers\SerieController;


Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('classe')->group(function () {
        Route::get('/', [ClasseController::class, 'paginate'])->name('classe.index');
        Route::get('/liste', [ClasseController::class, 'list'])->name('classe.list');
        Route::post('/', [ClasseController::class, 'store'])->name('classe.store');
        Route::get('/{id}', [ClasseController::class, 'show'])->name('classe.show');
        Route::put('/{id}', [ClasseController::class, 'update'])->name('classe.update');
        Route::delete('/{id}', [ClasseController::class, 'destroy'])->name('classe.destroy');
    });

    Route::prefix('parents')->group(function () {
        Route::get('/', [ParentController::class, 'paginate'])->name('parents.index');
        Route::get('/liste', [ParentController::class, 'list'])->name('parents.list');
        Route::post('/', [ParentController::class, 'store'])->name('parents.store');
        Route::get('/{parent}/eleves', [ParentController::class, 'eleves'])->name('parents.eleves');
        Route::get('/{parent}', [ParentController::class, 'show'])->name('parents.show');
        Route::put('/{parent}', [ParentController::class, 'update'])->name('parents.update');
        Route::delete('/{parent}', [ParentController::class, 'destroy'])->name('parents.destroy');
    });

    Route::prefix('eleves')->group(function () {
        Route::post('/', [EleveController::class, 'store'])->name('eleves.store');
        Route::get('/liste', [EleveController::class, 'list'])->name('eleves.liste');
        Route::get('search/{keyword}', [EleveController::class, 'search'])->name('eleves.search');
        Route::get('/', [EleveController::class, 'index'])->name('eleves.index');
        Route::get('/{eleves}', [EleveController::class, 'show'])->name('eleves.show');
        Route::put('/{eleves}', [EleveController::class, 'update'])->name('eleves.update');
        Route::delete('/{eleves}', [EleveController::class, 'destroy'])->name('eleves.destroy');
    });


    Route::prefix('cycle')->group(function () {
        Route::get('/', [CycleController::class, 'paginate'])->name('cycle.index');
        Route::get('/liste', [CycleController::class, 'list'])->name('cycle.list');
        Route::post('/', [CycleController::class, 'store'])->name('cycle.store');
        Route::get('/{id}', [CycleController::class, 'show'])->name('cycle.show');
        Route::put('/{id}', [CycleController::class, 'update'])->name('cycle.update');
        Route::delete('/{id}', [CycleController::class, 'destroy'])->name('cycle.destroy');
    });

    Route::prefix('serie')->group(function () {
        Route::get('/', [SerieController::class, 'paginate'])->name('serie.index');
        Route::get('/liste', [SerieController::class, 'list'])->name('serie.list');
        Route::post('/', [SerieController::class, 'store'])->name('serie.store');
        Route::get('/{id}', [SerieController::class, 'show'])->name('serie.show');
        Route::put('/{id}', [SerieController::class, 'update'])->name('serie.update');
        Route::delete('/{id}', [SerieController::class, 'destroy'])->name('serie.destroy');
    });
});
