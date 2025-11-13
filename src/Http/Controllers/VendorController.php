<?php

namespace Botble\FiberhomeOltManager\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class VendorController extends BaseController
{
    /**
     * Get all vendors
     */
    public function getVendors()
    {
        $vendors = config('olt-vendors.vendors', []);
        
        $result = [];
        foreach ($vendors as $key => $vendor) {
            $result[] = [
                'value' => $key,
                'text' => $vendor['name'],
                'models_count' => count($vendor['models'] ?? []),
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
    
    /**
     * Get models for a specific vendor
     */
    public function getModels(Request $request, $vendor)
    {
        $vendors = config('olt-vendors.vendors', []);
        
        if (!isset($vendors[$vendor])) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found',
            ], 404);
        }
        
        $models = $vendors[$vendor]['models'] ?? [];
        
        $result = [];
        foreach ($models as $key => $model) {
            $result[] = [
                'value' => $key,
                'text' => $model['name'],
                'description' => $model['description'] ?? '',
                'max_ports' => $model['max_ports'] ?? null,
                'max_onus' => $model['max_onus'] ?? null,
                'technology' => $model['technology'] ?? [],
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
    
    /**
     * Get model details
     */
    public function getModelDetails(Request $request, $vendor, $model)
    {
        $vendors = config('olt-vendors.vendors', []);
        
        if (!isset($vendors[$vendor])) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found',
            ], 404);
        }
        
        if (!isset($vendors[$vendor]['models'][$model])) {
            return response()->json([
                'success' => false,
                'message' => 'Model not found',
            ], 404);
        }
        
        $modelData = $vendors[$vendor]['models'][$model];
        
        return response()->json([
            'success' => true,
            'data' => [
                'vendor' => $vendor,
                'vendor_name' => $vendors[$vendor]['name'],
                'model' => $model,
                'name' => $modelData['name'],
                'description' => $modelData['description'] ?? '',
                'max_ports' => $modelData['max_ports'] ?? null,
                'max_onus' => $modelData['max_onus'] ?? null,
                'technology' => $modelData['technology'] ?? [],
            ],
        ]);
    }
}