<?php

use App\Http\Controllers\DeviceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Device Management Routes
Route::apiResource('devices', DeviceController::class);

// Device Operations
Route::prefix('devices/{device}')->group(function () {
    Route::post('/sync', [DeviceController::class, 'sync']);
    Route::post('/sync-ports', [DeviceController::class, 'syncPorts']);
    Route::post('/sync-onts/{portNumber}', [DeviceController::class, 'syncOnts']);
    Route::post('/add-ont', [DeviceController::class, 'addOnt']);
    Route::delete('/delete-ont', [DeviceController::class, 'deleteOnt']);
});

// Health Check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});