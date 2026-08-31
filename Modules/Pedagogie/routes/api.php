<?php

use Illuminate\Support\Facades\Route;
use Modules\Pedagogie\Http\Controllers\PedagogieController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pedagogies', PedagogieController::class)->names('pedagogie');
});
