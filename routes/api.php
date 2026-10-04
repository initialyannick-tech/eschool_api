<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\EnseignantController;
use Modules\Pedagogie\Http\Controllers\MatiereController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('enseignants')->group(function () {
        Route::get('/', [EnseignantController::class, 'index'])->name('enseignants.index');
        Route::get('/paginate', [EnseignantController::class, 'paginate'])->name('enseignants.paginate');
        Route::get('/{id}', [EnseignantController::class, 'show'])->name('enseignants.show');
        Route::post('/', [EnseignantController::class, 'store'])->name('enseignants.store');
        Route::put('/{id}', [EnseignantController::class, 'update'])->name('enseignants.update');
        Route::delete('/{id}', [EnseignantController::class, 'destroy'])->name('enseignants.destroy');
    });

    Route::prefix('matiere')->group(function () {
        Route::get('/', [MatiereController::class, 'paginate'])->name('matiere.index');
        Route::get('/liste', [MatiereController::class, 'list'])->name('matiere.list');
        Route::post('/', [MatiereController::class, 'store'])->name('matiere.store');
        Route::get('/{id}', [MatiereController::class, 'show'])->name('matiere.show');
        Route::put('/{id}', [MatiereController::class, 'update'])->name('matiere.update');
        Route::delete('/{id}', [MatiereController::class, 'destroy'])->name('matiere.destroy');
        Route::get('/{id}/enseignants', [MatiereController::class, 'enseignants'])->name('matiere.enseignants');
        Route::post('/{id}/enseignants', [MatiereController::class, 'assignerEnseignants'])->name('matiere.assigner.enseignants');
    });
});
