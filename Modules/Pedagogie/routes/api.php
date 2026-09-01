<?php

use Illuminate\Support\Facades\Route;
use Modules\Pedagogie\Http\Controllers\ClasseController;




Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('classe')->group(function () {
        Route::get('/', [ClasseController::class, 'paginate'])->name('classe.index');
        Route::get('/liste', [ClasseController::class, 'list'])->name('classe.list');
        Route::post('/', [ClasseController::class, 'store'])->name('classe.store');
        Route::get('/{id}', [ClasseController::class, 'show'])->name('classe.show');
        Route::put('/{id}', [ClasseController::class, 'update'])->name('classe.update');
        Route::delete('/{id}', [ClasseController::class, 'destroy'])->name('classe.destroy');
    });
});
