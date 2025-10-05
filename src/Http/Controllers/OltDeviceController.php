<?php

namespace Botble\FiberhomeOltManager\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\FiberhomeOltManager\Models\OltDevice;
use Botble\FiberhomeOltManager\Services\SnmpManager;
use Botble\FiberhomeOltManager\Services\OltDataCollector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Botble\FiberhomeOltManager\Tables\OltDeviceTable;

class OltDeviceController extends BaseController
{
    protected SnmpManager $snmp;
    protected OltDataCollector $collector;

    public function __construct(SnmpManager $snmp, OltDataCollector $collector)
    {
        $this->snmp = $snmp;
        $this->collector = $collector;
    }

    public function index(Request $request)
    {
        page_title()->setTitle('OLT Devices');

        $devices = OltDevice::orderBy('created_at', 'desc')->paginate(20);

        return view('plugins/fiberhome-olt-manager::devices.index', compact('devices'));
    }

    public function create()
    {
        page_title()->setTitle('Add OLT Device');

        return view('plugins/fiberhome-olt-manager::devices.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:olt_devices',
            'snmp_community' => 'required|string|max:255',
            'snmp_version' => 'required|in:1,2c,3',
            'snmp_port' => 'required|integer|min:1|max:65535',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $device = OltDevice::create($request->all());

        // Test connection
        if ($this->snmp->testConnection($device)) {
            $device->update(['status' => 'online']);
            
            // Collect initial data
            $this->collector->collectAll($device);
        }

        return redirect()
            ->route('fiberhome-olt.devices.show', $device->id)
            ->with('success', 'OLT device added successfully');
    }

    public function show($id)
    {
        $device = OltDevice::with(['cards', 'ponPorts', 'onus'])->findOrFail($id);
        
        page_title()->setTitle($device->name);

        // Get performance data for last 24 hours
        $performanceData = $device->performanceLogs()
            ->where('recorded_at', '>=', now()->subDay())
            ->orderBy('recorded_at')
            ->get();

        return view('plugins/fiberhome-olt-manager::devices.show', compact('device', 'performanceData'));
    }

    public function edit($id)
    {
        $device = OltDevice::findOrFail($id);
        
        page_title()->setTitle('Edit ' . $device->name);

        return view('plugins/fiberhome-olt-manager::devices.edit', compact('device'));
    }

    public function update(Request $request, $id)
    {
        $device = OltDevice::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:olt_devices,ip_address,' . $id,
            'snmp_community' => 'required|string|max:255',
            'snmp_version' => 'required|in:1,2c,3',
            'snmp_port' => 'required|integer|min:1|max:65535',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $device->update($request->all());

        return redirect()
            ->route('fiberhome-olt.devices.show', $device->id)
            ->with('success', 'OLT device updated successfully');
    }

    public function destroy($id)
    {
        $device = OltDevice::findOrFail($id);
        $device->delete();

        return redirect()
            ->route('fiberhome-olt.devices.index')
            ->with('success', 'OLT device deleted successfully');
    }

    public function sync(Request $request, $id)
    {
        $device = OltDevice::findOrFail($id);

        if ($this->collector->collectAll($device)) {
            return response()->json([
                'success' => true,
                'message' => 'Data synchronized successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to synchronize data',
        ], 500);
    }

    public function testConnection(Request $request, $id)
    {
        $device = OltDevice::findOrFail($id);

        if ($this->snmp->testConnection($device)) {
            return response()->json([
                'success' => true,
                'message' => 'Connection successful',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Connection failed',
        ], 500);
    }

    /**
     * Get DataTable for OLT devices
     */
    public function getTable(Request $request)
    {
        return app(OltDeviceTable::class)->render();
    }
}