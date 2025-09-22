<?php

namespace App\Services;

use App\Models\Device;
use App\Services\Vendors\VendorAdapterInterface;
use Illuminate\Support\Facades\Log;

class DeviceManager
{
    public function getAdapter(Device $device): VendorAdapterInterface
    {
        $adapterClass = $device->getVendorAdapterClass();
        
        if (!class_exists($adapterClass)) {
            throw new \InvalidArgumentException("Adapter class {$adapterClass} not found");
        }
        
        return new $adapterClass($device);
    }

    public function syncDeviceInfo(Device $device): bool
    {
        try {
            $adapter = $this->getAdapter($device);
            
            if (!$adapter->connect()) {
                Log::error("Failed to connect to device {$device->id}");
                return false;
            }

            $deviceInfo = $adapter->getDeviceInfo();
            
            $device->update([
                'firmware_version' => $deviceInfo['firmware_version'] ?? null,
                'serial_number' => $deviceInfo['serial_number'] ?? null,
                'status' => true,
                'last_seen_at' => now(),
            ]);

            $adapter->disconnect();
            
            Log::info("Successfully synced device info for device {$device->id}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Error syncing device info for device {$device->id}: " . $e->getMessage());
            
            $device->update([
                'status' => false,
                'last_seen_at' => now(),
            ]);
            
            return false;
        }
    }

    public function syncPorts(Device $device): bool
    {
        try {
            $adapter = $this->getAdapter($device);
            
            if (!$adapter->connect()) {
                return false;
            }

            $ports = $adapter->getPortList();
            
            foreach ($ports as $portData) {
                $device->ports()->updateOrCreate(
                    ['port_number' => $portData['port_number']],
                    [
                        'port_type' => $portData['port_type'] ?? 'GPON',
                        'status' => $portData['status'] ?? false,
                        'admin_status' => $portData['admin_status'] ?? false,
                        'operational_status' => $portData['operational_status'] ?? false,
                        'ont_count' => $portData['ont_count'] ?? 0,
                        'max_ont_count' => $portData['max_ont_count'] ?? 128,
                    ]
                );
            }

            $adapter->disconnect();
            
            Log::info("Successfully synced ports for device {$device->id}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Error syncing ports for device {$device->id}: " . $e->getMessage());
            return false;
        }
    }

    public function syncOnts(Device $device, int $portNumber): bool
    {
        try {
            $adapter = $this->getAdapter($device);
            
            if (!$adapter->connect()) {
                return false;
            }

            $port = $device->ports()->where('port_number', $portNumber)->first();
            if (!$port) {
                Log::warning("Port {$portNumber} not found for device {$device->id}");
                return false;
            }

            $onts = $adapter->getOntList($portNumber);
            
            foreach ($onts as $ontData) {
                $device->onts()->updateOrCreate(
                    [
                        'port_id' => $port->id,
                        'ont_id' => $ontData['ont_id']
                    ],
                    [
                        'serial_number' => $ontData['serial_number'] ?? null,
                        'mac_address' => $ontData['mac_address'] ?? null,
                        'model' => $ontData['model'] ?? null,
                        'status' => $ontData['status'] ?? false,
                        'operational_status' => $ontData['operational_status'] ?? false,
                        'rx_power' => $ontData['rx_power'] ?? null,
                        'tx_power' => $ontData['tx_power'] ?? null,
                        'distance' => $ontData['distance'] ?? null,
                        'last_seen_at' => now(),
                    ]
                );
            }

            $adapter->disconnect();
            
            Log::info("Successfully synced ONTs for device {$device->id}, port {$portNumber}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Error syncing ONTs for device {$device->id}, port {$portNumber}: " . $e->getMessage());
            return false;
        }
    }

    public function addOnt(Device $device, int $portNumber, string $serialNumber, array $config = []): bool
    {
        try {
            $adapter = $this->getAdapter($device);
            
            if (!$adapter->connect()) {
                return false;
            }

            $result = $adapter->addOnt($portNumber, $serialNumber, $config);
            $adapter->disconnect();
            
            if ($result) {
                // Sync ONTs after successful addition
                $this->syncOnts($device, $portNumber);
            }
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error("Error adding ONT to device {$device->id}: " . $e->getMessage());
            return false;
        }
    }

    public function deleteOnt(Device $device, int $portNumber, int $ontId): bool
    {
        try {
            $adapter = $this->getAdapter($device);
            
            if (!$adapter->connect()) {
                return false;
            }

            $result = $adapter->deleteOnt($portNumber, $ontId);
            $adapter->disconnect();
            
            if ($result) {
                // Remove from database
                $device->onts()
                    ->whereHas('port', function($query) use ($portNumber) {
                        $query->where('port_number', $portNumber);
                    })
                    ->where('ont_id', $ontId)
                    ->delete();
            }
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error("Error deleting ONT from device {$device->id}: " . $e->getMessage());
            return false;
        }
    }
}