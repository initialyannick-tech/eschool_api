<?php

use Illuminate\Support\Facades\Route;
use Modules\Parametre\Http\Controllers\AnneeScolaireController;



Route::middleware(['auth:sanctum'])->group(function () {

    Route::prefix('anneeScolaire')->group(function () {

        Route::get('/', [AnneeScolaireController::class, 'paginate'])->name('annee.scolaire.index');
        Route::get('/liste', [AnneeScolaireController::class, 'list'])->name('annee.scolaire.list');
        Route::get('/active', [AnneeScolaireController::class, 'active'])->name('annee.scolaire.active');
        Route::post('/', [AnneeScolaireController::class, 'store'])->name('annee.scolaire.store');
        Route::post('/preparer-suivante', [AnneeScolaireController::class, 'preparerSuivante'])->name('annee.scolaire.preparer.suivante');
        Route::get('/{anneeScolaire}', [AnneeScolaireController::class, 'show'])->name('annee.scolaire.show');
        Route::put('/{anneeScolaire}', [AnneeScolaireController::class, 'update'])->name('annee.scolaire.update');
        Route::delete('/{anneeScolaire}', [AnneeScolaireController::class, 'destroy'])->name('annee.scolaire.destroy');
        Route::post('/{anneeScolaire}/ouvrir', [AnneeScolaireController::class,'ouvrir' ])->name('annee.scolaire.ouvrir');
        Route::post('/{anneeScolaire}/activer', [AnneeScolaireController::class, 'activer'])->name('annee.scolaire.activer');
        Route::post('/{anneeScolaire}/cloturer', [AnneeScolaireController::class,'cloturer'])->name('annee.scolaire.cloturer');
    });
});
