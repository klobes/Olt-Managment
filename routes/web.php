<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;
use Botble\FiberhomeOltManager\Http\Controllers\DashboardController;
use Botble\FiberhomeOltManager\Http\Controllers\OltController;
use Botble\FiberhomeOltManager\Http\Controllers\OnuController;
use Botble\FiberhomeOltManager\Http\Controllers\BandwidthProfileController;

AdminHelper::registerRoutes(function () {
//Route::group(['middleware' => ['web', 'api']], function () {//core

    Route::group(['prefix' => 'fiberhome-olt', 'as' => 'fiberhome-olt.','middleware' => ['web', 'api']], function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        
        
        
        
        // Vendor Management (v1.5.0)
        Route::group(['prefix' => 'vendor', 'as' => 'vendor.'], function () {
            Route::get('configurations', [VendorController::class, 'configurations'])->name('configurations');
            Route::get('configurations/create', [VendorController::class, 'createConfiguration'])->name('create-configuration');
            Route::post('configurations/create', [VendorController::class, 'storeConfiguration'])->name('store-configuration');
            
            Route::get('onu-types', [VendorController::class, 'onuTypes'])->name('onu-types');
            Route::get('onu-types/create', [VendorController::class, 'createOnuType'])->name('create-onu-type');
            Route::post('onu-types/create', [VendorController::class, 'storeOnuType'])->name('store-onu-type');
        });
        
        // OLT Devices
        Route::group(['prefix' => 'devices', 'as' => 'devices.'], function () {
            Route::get('/', [OltController::class, 'index'])->name('index');
            Route::get('create', [OltController::class, 'create'])->name('create');
            Route::post('create', [OltController::class, 'store'])->name('store');
            Route::get('{id}', [OltController::class, 'show'])->name('show');
            Route::get('{id}/edit', [OltController::class, 'edit'])->name('edit');
            Route::put('{id}', [OltController::class, 'update'])->name('update');
            Route::delete('{id}', [OltController::class, 'destroy'])->name('destroy');
            Route::post('{id}/sync', [OltController::class, 'sync'])->name('sync');
            Route::post('{id}/test-connection', [OltController::class, 'testConnection'])->name('test-connection');
        });
        
        // ONUs
        Route::group(['prefix' => 'onus', 'as' => 'onus.'], function () {
            Route::get('/', [OnuController::class, 'index'])->name('index');
            Route::get('create', [OnuController::class, 'create'])->name('create');
            Route::post('create', [OnuController::class, 'store'])->name('store');
            Route::get('{id}', [OnuController::class, 'show'])->name('show');
            Route::get('{id}/edit', [OnuController::class, 'edit'])->name('edit');
            Route::put('{id}', [OnuController::class, 'update'])->name('update');
            Route::delete('{id}', [OnuController::class, 'destroy'])->name('destroy');
            Route::post('{id}/enable', [OnuController::class, 'enable'])->name('enable');
            Route::post('{id}/disable', [OnuController::class, 'disable'])->name('disable');
            Route::post('{id}/reboot', [OnuController::class, 'reboot'])->name('reboot');
        });
        
        // Bandwidth Profiles
        Route::group(['prefix' => 'bandwidth-profiles', 'as' => 'bandwidth-profiles.'], function () {
            Route::get('/', [BandwidthProfileController::class, 'index'])->name('index');
            Route::get('create', [BandwidthProfileController::class, 'create'])->name('create');
            Route::post('create', [BandwidthProfileController::class, 'store'])->name('store');
            Route::get('{id}/edit', [BandwidthProfileController::class, 'edit'])->name('edit');
            Route::put('{id}', [BandwidthProfileController::class, 'update'])->name('update');
            Route::delete('{id}', [BandwidthProfileController::class, 'destroy'])->name('destroy');
        });
        
        // DataTables Routes
        Route::group(['prefix' => 'datatables', 'as' => 'datatables.'], function () {
            Route::post('devices', [OltController::class, 'getTable'])->name('devices.table');
            Route::post('onus', [OnuController::class, 'getTable'])->name('onus.table');
            Route::post('bandwidth-profiles', [BandwidthProfileController::class, 'getTable'])->name('bandwidth-profiles.table');
        });
    });
//});
});
