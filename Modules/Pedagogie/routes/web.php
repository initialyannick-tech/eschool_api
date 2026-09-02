<?php

use Illuminate\Support\Facades\Route;
use Modules\Pedagogie\Http\Controllers\EleveController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('pedagogies', EleveController::class)->names('pedagogie');
});
