<?php

namespace App\Services\Vendors;

use App\Models\Device;

interface VendorAdapterInterface
{
    public function __construct(Device $device);
    
    public function connect(): bool;
    
    public function disconnect(): void;
    
    public function getDeviceInfo(): array;
    
    public function getPortList(): array;
    
    public function getPortInfo(int $portNumber): array;
    
    public function getOntList(int $portNumber): array;
    
    public function getOntInfo(int $portNumber, int $ontId): array;
    
    public function addOnt(int $portNumber, string $serialNumber, array $config = []): bool;
    
    public function deleteOnt(int $portNumber, int $ontId): bool;
    
    public function configureOnt(int $portNumber, int $ontId, array $config): bool;
    
    public function enablePort(int $portNumber): bool;
    
    public function disablePort(int $portNumber): bool;
    
    public function getSystemStatus(): array;
    
    public function getAlarms(): array;
    
    public function executeCommand(string $command): string;
}