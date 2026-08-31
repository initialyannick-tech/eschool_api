<?php

use Illuminate\Support\Facades\Route;
use Modules\Pedagogie\Http\Controllers\PedagogieController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('pedagogies', PedagogieController::class)->names('pedagogie');
});
