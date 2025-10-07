<?php

use Illuminate\Support\Facades\Route;
use Botble\FiberhomeOltManager\Http\Controllers\OltDeviceController;
use Botble\FiberhomeOltManager\Http\Controllers\ONUController;
use Botble\FiberhomeOltManager\Http\Controllers\BandwidthProfileController;
use Botble\FiberhomeOltManager\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the FiberHome OLT Manager.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

Route::group([
    'prefix' => 'fiberhome-olt',
    'as' => 'api.fiberhome-olt.',
    'middleware' => ['api', 'auth:sanctum']
], function () {
    
    // Dashboard API
    Route::get('dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');
    
    // OLT Device API
    Route::group(['prefix' => 'devices', 'as' => 'devices.'], function () {
        Route::get('/', [OltDeviceController::class, 'index'])->name('index');
        Route::post('/', [OltDeviceController::class, 'store'])->name('store');
        Route::post('test-connection', [OltDeviceController::class, 'testConnection'])->name('test-connection');
        Route::get('{id}', [OltDeviceController::class, 'getDetails'])->name('show');
        Route::put('{id}', [OltDeviceController::class, 'update'])->name('update');
        Route::delete('{id}', [OltDeviceController::class, 'destroy'])->name('destroy');
        Route::post('{id}/sync', [OltDeviceController::class, 'sync'])->name('sync');
        Route::post('{id}/test-connection', [OltDeviceController::class, 'testConnection'])->name('test-connection-existing');
        Route::post('datatable', [OltDeviceController::class, 'getTable'])->name('datatable');
    });
    
    // ONU API
    Route::group(['prefix' => 'onus', 'as' => 'onus.'], function () {
        Route::get('/', [ONUController::class, 'index'])->name('index');
        Route::get('available', [ONUController::class, 'available'])->name('available');
        Route::get('{id}', [ONUController::class, 'show'])->name('show');
        Route::put('{id}', [ONUController::class, 'update'])->name('update');
        Route::delete('{id}', [ONUController::class, 'destroy'])->name('destroy');
        Route::post('{id}/enable', [ONUController::class, 'enable'])->name('enable');
        Route::post('{id}/disable', [ONUController::class, 'disable'])->name('disable');
        Route::post('{id}/reboot', [ONUController::class, 'reboot'])->name('reboot');
        Route::post('{id}/configure', [ONUController::class, 'configure'])->name('configure');
        Route::get('{id}/performance', [ONUController::class, 'performance'])->name('performance');
        Route::post('datatable', [ONUController::class, 'getTable'])->name('datatable');
    });
    
    // Bandwidth Profile API
    Route::group(['prefix' => 'bandwidth-profiles', 'as' => 'bandwidth-profiles.'], function () {
        Route::get('/', [BandwidthProfileController::class, 'index'])->name('index');
        Route::post('/', [BandwidthProfileController::class, 'store'])->name('store');
        Route::get('{id}', [BandwidthProfileController::class, 'show'])->name('show');
        Route::put('{id}', [BandwidthProfileController::class, 'update'])->name('update');
        Route::delete('{id}', [BandwidthProfileController::class, 'destroy'])->name('destroy');
        Route::post('{id}/assign', [BandwidthProfileController::class, 'assign'])->name('assign');
        Route::post('datatable', [BandwidthProfileController::class, 'getTable'])->name('datatable');
    });
    
    // Vendor API
    Route::group(['prefix' => 'vendors', 'as' => 'vendors.'], function () {
        Route::get('/', [\Botble\FiberhomeOltManager\Http\Controllers\VendorController::class, 'getVendors'])->name('list');
        Route::get('{vendor}/models', [\Botble\FiberhomeOltManager\Http\Controllers\VendorController::class, 'getModels'])->name('models');
        Route::get('{vendor}/models/{model}', [\Botble\FiberhomeOltManager\Http\Controllers\VendorController::class, 'getModelDetails'])->name('model-details');
    });
});