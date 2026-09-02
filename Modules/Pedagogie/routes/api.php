<?php

use Illuminate\Support\Facades\Route;
use Modules\Pedagogie\Http\Controllers\ClasseController;
use Modules\Pedagogie\Http\Controllers\EleveController;
use Modules\Pedagogie\Http\Controllers\ParentController;


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
        Route::get('/', [EleveController::class, 'paginate'])->name('eleves.index');
        Route::get('/liste', [EleveController::class, 'list'])->name('eleves.list');
        Route::post('/', [EleveController::class, 'store'])->name('eleves.store');
        Route::get('/parent/{parentId}', [EleveController::class, 'byParent'])->name('eleves.by.parent');
        Route::put('/{eleveId}/parent', [EleveController::class, 'assignerParent'])->name('eleves.assigner.parent');
        Route::delete('/{eleveId}/parent', [EleveController::class, 'retirerParent'])->name('eleves.retirer.parent');
        Route::get('/{id}', [EleveController::class, 'show'])->name('eleves.show');
        Route::put('/{id}', [EleveController::class, 'update'])->name('eleves.update');
        Route::delete('/{id}', [EleveController::class, 'destroy'])->name('eleves.destroy');
    });
});
