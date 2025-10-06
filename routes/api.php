<?php

use Illuminate\Support\Facades\Route;
use Botble\FiberhomeOltManager\Http\Controllers\OltController;
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
        Route::get('/', [OltController::class, 'index'])->name('index');
        Route::post('/', [OltController::class, 'store'])->name('store');
        Route::post('test-connection', [OltController::class, 'testConnection'])->name('test-connection');
        Route::get('{id}', [OltController::class, 'getDetails'])->name('show');
        Route::put('{id}', [OltController::class, 'update'])->name('update');
        Route::delete('{id}', [OltController::class, 'destroy'])->name('destroy');
        Route::post('{id}/sync', [OltController::class, 'sync'])->name('sync');
        Route::post('{id}/test-connection', [OltController::class, 'testConnection'])->name('test-connection-existing');
        Route::post('datatable', [OltController::class, 'getTable'])->name('datatable');
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
});