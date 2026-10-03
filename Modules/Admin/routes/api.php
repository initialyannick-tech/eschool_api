<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AuthController;
use Modules\Admin\Http\Controllers\RoleController;
use Modules\Admin\Http\Controllers\SpecialiteController;
use Modules\Admin\Http\Controllers\UserController;


Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::put('auth/change-password', [UserController::class, 'updatePassword'])->name('user.update.password');

    Route::prefix('role')->group(function() {
        Route::get('/', [RoleController::class, 'paginate'])->name('role.index');
        Route::get('/liste', [RoleController::class, 'index'])->name('role.list');
        Route::get('/permissions', [RoleController::class, 'permissions'])->name('role.permissions');
        Route::post('/', [RoleController::class, 'store'])->name('role.store');
        Route::put('/{role}', [RoleController::class, 'update'])->name('role.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('role.destroy');
    });

    Route::prefix('specialites')->group(function () {
        Route::get('/', [SpecialiteController::class, 'index'])->name('specialites.index');
    });

    Route::prefix('enseignants')->group(function () {
        Route::get('/', [\Modules\Admin\Http\Controllers\EnseignantController::class, 'index'])->name('enseignants.index');
        Route::get('/paginate', [\Modules\Admin\Http\Controllers\EnseignantController::class, 'paginate'])->name('enseignants.paginate');
        Route::get('/{id}', [\Modules\Admin\Http\Controllers\EnseignantController::class, 'show'])->name('enseignants.show');
        Route::post('/', [\Modules\Admin\Http\Controllers\EnseignantController::class, 'store'])->name('enseignants.store');
        Route::put('/{id}', [\Modules\Admin\Http\Controllers\EnseignantController::class, 'update'])->name('enseignants.update');
        Route::delete('/{id}', [\Modules\Admin\Http\Controllers\EnseignantController::class, 'destroy'])->name('enseignants.destroy');
    });

    Route::prefix('userList')->group(function() {
        Route::get('/', [UserController::class, 'paginate'])->name('user.index');
        Route::get('/enseignant', [UserController::class, 'enseignants'])->name('enseignants.index');
        Route::get('/liste', [UserController::class, 'list'])->name('user.list');
        Route::get('/{user}', [UserController::class, 'show'])->name('user.show');
        Route::post('/', [UserController::class, 'store'])->name('user.store');
        Route::put('/{user}', [UserController::class, 'update'])->name('user.update');
    });
});
