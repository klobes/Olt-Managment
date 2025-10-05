<?php

namespace Botble\FiberhomeOltManager\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\FiberhomeOltManager\Models\VendorConfiguration;
use Botble\FiberhomeOltManager\Models\OnuType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VendorController extends BaseController
{
    public function configurations(Request $request)
    {
        page_title()->setTitle('Vendor Configurations');

        $configurations = VendorConfiguration::orderBy('vendor')->orderBy('model')->get();

        return view('plugins/fiberhome-olt-manager::vendor.configurations', compact('configurations'));
    }

    public function createConfiguration(Request $request)
    {
        page_title()->setTitle('Add Vendor Configuration');

        $vendors = ['fiberhome', 'huawei', 'zte', 'other'];

        return view('plugins/fiberhome-olt-manager::vendor.create-configuration', compact('vendors'));
    }

    public function storeConfiguration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor' => 'required|in:fiberhome,huawei,zte,other',
            'model' => 'required|string|max:255',
            'oid_mappings' => 'required|array',
            'capabilities' => 'required|array',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        VendorConfiguration::create([
            'vendor' => $request->vendor,
            'model' => $request->model,
            'oid_mappings' => $request->oid_mappings,
            'capabilities' => $request->capabilities,
            'default_settings' => $request->default_settings ?? null,
            'notes' => $request->notes ?? null,
        ]);

        return redirect()
            ->route('fiberhome-olt.vendor.configurations')
            ->with('success', 'Vendor configuration created successfully');
    }

    public function onuTypes(Request $request)
    {
        page_title()->setTitle('ONU Types');

        $onuTypes = OnuType::with('vendorConfiguration')
            ->orderBy('vendor')
            ->orderBy('model')
            ->get();

        return view('plugins/fiberhome-olt-manager::vendor.onu-types', compact('onuTypes'));
    }

    public function createOnuType(Request $request)
    {
        page_title()->setTitle('Add ONU Type');

        $vendors = ['fiberhome', 'huawei', 'zte', 'other'];
        $configurations = VendorConfiguration::orderBy('vendor')->orderBy('model')->get();

        return view('plugins/fiberhome-olt-manager::vendor.create-onu-type', compact('vendors', 'configurations'));
    }

    public function storeOnuType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor' => 'required|in:fiberhome,huawei,zte,other',
            'model' => 'required|string|max:255',
            'type_name' => 'required|string|max:255',
            'ethernet_ports' => 'required|integer|min:0',
            'pots_ports' => 'required|integer|min:0',
            'catv_ports' => 'required|integer|min:0',
            'wifi_support' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        OnuType::create([
            'vendor' => $request->vendor,
            'model' => $request->model,
            'type_name' => $request->type_name,
            'ethernet_ports' => $request->ethernet_ports,
            'pots_ports' => $request->pots_ports,
            'catv_ports' => $request->catv_ports,
            'wifi_support' => $request->wifi_support ?? false,
            'capabilities' => $request->capabilities ?? null,
            'default_config' => $request->default_config ?? null,
            'description' => $request->description ?? null,
        ]);

        return redirect()
            ->route('fiberhome-olt.vendor.onu-types')
            ->with('success', 'ONU type created successfully');
    }
}