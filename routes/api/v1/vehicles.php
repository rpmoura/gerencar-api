<?php

use App\Http\Controllers\V1\VehicleController;
use Illuminate\Support\Facades\Route;

Route::prefix('vehicles')->group(function () {
    Route::post('/', [VehicleController::class, 'post']);
    Route::get('/{uuid}', [VehicleController::class, 'find']);
    Route::get('/', [VehicleController::class, 'get']);
    Route::put('/{uuid}', [VehicleController::class, 'put']);
    Route::delete('/{uuid}', [VehicleController::class, 'delete']);
});
