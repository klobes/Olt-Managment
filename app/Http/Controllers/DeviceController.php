<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Services\DeviceManager;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
    public function __construct(
        private DeviceManager $deviceManager
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Device::with(['ports', 'onts']);

        if ($request->has('vendor')) {
            $query->byVendor($request->vendor);
        }

        if ($request->has('status')) {
            if ($request->boolean('status')) {
                $query->active();
            } else {
                $query->where('status', false);
            }
        }

        $devices = $query->paginate($request->get('per_page', 15));

        return response()->json($devices);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'vendor' => ['required', Rule::in(['fiberhome', 'huawei', 'zte', 'other'])],
            'model' => 'nullable|string|max:255',
            'ip_address' => 'required|ip',
            'snmp_community' => 'nullable|string|max:255',
            'snmp_version' => ['nullable', Rule::in(['1', '2c', '3'])],
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'port_count' => 'nullable|integer|min:1|max:64',
        ]);

        $device = Device::create($validated);

        // Try to sync device info immediately
        $this->deviceManager->syncDeviceInfo($device);

        return response()->json($device->load(['ports', 'onts']), 201);
    }

    public function show(Device $device): JsonResponse
    {
        return response()->json($device->load(['ports.onts', 'monitoringLogs' => function($query) {
            $query->recent(24)->orderBy('recorded_at', 'desc')->limit(100);
        }]));
    }

    public function update(Request $request, Device $device): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'vendor' => ['sometimes', Rule::in(['fiberhome', 'huawei', 'zte', 'other'])],
            'model' => 'nullable|string|max:255',
            'ip_address' => 'sometimes|ip',
            'snmp_community' => 'nullable|string|max:255',
            'snmp_version' => ['nullable', Rule::in(['1', '2c', '3'])],
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'port_count' => 'nullable|integer|min:1|max:64',
        ]);

        $device->update($validated);

        return response()->json($device->load(['ports', 'onts']));
    }

    public function destroy(Device $device): JsonResponse
    {
        $device->delete();
        return response()->json(['message' => 'Device deleted successfully']);
    }

    public function sync(Device $device): JsonResponse
    {
        $deviceSynced = $this->deviceManager->syncDeviceInfo($device);
        $portsSynced = $this->deviceManager->syncPorts($device);

        return response()->json([
            'message' => 'Sync completed',
            'device_synced' => $deviceSynced,
            'ports_synced' => $portsSynced,
            'device' => $device->fresh(['ports', 'onts'])
        ]);
    }

    public function syncPorts(Device $device): JsonResponse
    {
        $success = $this->deviceManager->syncPorts($device);

        return response()->json([
            'message' => $success ? 'Ports synced successfully' : 'Failed to sync ports',
            'success' => $success,
            'ports' => $device->fresh()->ports
        ]);
    }

    public function syncOnts(Device $device, int $portNumber): JsonResponse
    {
        $success = $this->deviceManager->syncOnts($device, $portNumber);

        $port = $device->ports()->where('port_number', $portNumber)->first();

        return response()->json([
            'message' => $success ? 'ONTs synced successfully' : 'Failed to sync ONTs',
            'success' => $success,
            'onts' => $port ? $port->onts : []
        ]);
    }

    public function addOnt(Request $request, Device $device): JsonResponse
    {
        $validated = $request->validate([
            'port_number' => 'required|integer|min:1',
            'serial_number' => 'required|string|max:255',
            'config' => 'nullable|array',
        ]);

        $success = $this->deviceManager->addOnt(
            $device,
            $validated['port_number'],
            $validated['serial_number'],
            $validated['config'] ?? []
        );

        return response()->json([
            'message' => $success ? 'ONT added successfully' : 'Failed to add ONT',
            'success' => $success
        ]);
    }

    public function deleteOnt(Request $request, Device $device): JsonResponse
    {
        $validated = $request->validate([
            'port_number' => 'required|integer|min:1',
            'ont_id' => 'required|integer|min:1',
        ]);

        $success = $this->deviceManager->deleteOnt(
            $device,
            $validated['port_number'],
            $validated['ont_id']
        );

        return response()->json([
            'message' => $success ? 'ONT deleted successfully' : 'Failed to delete ONT',
            'success' => $success
        ]);
    }
}