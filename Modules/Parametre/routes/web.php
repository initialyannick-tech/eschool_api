<?php

use Illuminate\Support\Facades\Route;
use Modules\Parametre\Http\Controllers\ParametreController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('parametres', ParametreController::class)->names('parametre');
});
