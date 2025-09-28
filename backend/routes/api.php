<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ServerController;


Route::apiResource('servers', ServerController::class);
Route::patch('update-order-server', [ServerController::class, 'updateOrder']);


