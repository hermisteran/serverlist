<?php

use App\Http\Controllers\API\ServerController;
use Illuminate\Support\Facades\Route;

Route::apiResource('servers', ServerController::class);
Route::patch('update-order-server', [ServerController::class, 'updateOrder']);
