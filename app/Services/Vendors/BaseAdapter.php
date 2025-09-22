<?php

namespace App\Services\Vendors;

use App\Models\Device;
use Illuminate\Support\Facades\Log;

abstract class BaseAdapter implements VendorAdapterInterface
{
    protected Device $device;
    protected $connection;
    protected bool $isConnected = false;

    public function __construct(Device $device)
    {
        $this->device = $device;
    }

    abstract public function connect(): bool;
    
    abstract public function disconnect(): void;

    protected function log(string $level, string $message, array $context = []): void
    {
        Log::log($level, "[{$this->device->vendor}] {$message}", array_merge([
            'device_id' => $this->device->id,
            'device_ip' => $this->device->ip_address,
        ], $context));
    }

    protected function logInfo(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    protected function logError(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    protected function logWarning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    protected function ensureConnected(): bool
    {
        if (!$this->isConnected) {
            return $this->connect();
        }
        return true;
    }

    protected function parseSnmpResponse(string $response): array
    {
        // Basic SNMP response parsing - to be overridden by vendor-specific implementations
        $lines = explode("\n", trim($response));
        $result = [];
        
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false) {
                [$oid, $value] = explode('=', $line, 2);
                $result[trim($oid)] = trim($value);
            }
        }
        
        return $result;
    }

    protected function formatMacAddress(string $mac): string
    {
        // Remove any existing separators and convert to standard format
        $mac = preg_replace('/[^a-fA-F0-9]/', '', $mac);
        return strtoupper(implode(':', str_split($mac, 2)));
    }

    protected function validateIpAddress(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }
}