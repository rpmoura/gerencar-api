<?php

use App\Http\Controllers\V1\{AssociateController, UserController};
use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(function () {
    Route::post('/', [UserController::class, 'post']);
    Route::get('/{uuid}', [UserController::class, 'find']);
    Route::get('/', [UserController::class, 'get']);
    Route::put('/{uuid}', [UserController::class, 'put']);
    Route::delete('/{uuid}', [UserController::class, 'delete']);

    Route::post('/{user}/vehicles/{vehicle}', [AssociateController::class, 'post']);
    Route::delete('/{user}/vehicles/{vehicle}', [AssociateController::class, 'delete']);
    Route::get('/{user}/vehicles', [AssociateController::class, 'get']);
});
