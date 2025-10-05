# 🌐 Multi-Vendor Support Guide

## Overview

Ky dokument përshkruan si të shtoni mbështetje për vendor të tjerë OLT (Huawei, ZTE, etj.) në plugin.

---

## Arkitektura e Multi-Vendor

### 1. Vendor Abstraction Layer

```
┌─────────────────────────────────────────┐
│         Unified API Interface           │
├─────────────────────────────────────────┤
│         Vendor Manager                  │
├─────────────────────────────────────────┤
│  ┌──────────┐ ┌──────────┐ ┌─────────┐ │
│  │Fiberhome │ │ Huawei   │ │  ZTE    │ │
│  │ Driver   │ │ Driver   │ │ Driver  │ │
│  └──────────┘ └──────────┘ └─────────┘ │
├─────────────────────────────────────────┤
│         SNMP Manager                    │
└─────────────────────────────────────────┘
```

---

## Shtimi i Huawei OLT Support

### Hapi 1: Krijoni Huawei Driver

```php
// src/Services/Vendors/HuaweiDriver.php
<?php

namespace Botble\FiberhomeOltManager\Services\Vendors;

use Botble\FiberhomeOltManager\Services\Vendors\VendorDriverInterface;
use Botble\FiberhomeOltManager\Models\OltDevice;

class HuaweiDriver implements VendorDriverInterface
{
    protected array $oids = [
        // Huawei-specific OIDs
        'system_info' => '1.3.6.1.4.1.2011.6.128.1.1.2',
        'onu_list' => '1.3.6.1.4.1.2011.6.128.1.1.2.43',
        'onu_status' => '1.3.6.1.4.1.2011.6.128.1.1.2.46',
        'optical_power' => '1.3.6.1.4.1.2011.6.128.1.1.2.51',
        // ... more OIDs
    ];

    public function getSystemInfo(OltDevice $olt): array
    {
        // Huawei-specific implementation
    }

    public function getOnuList(OltDevice $olt): array
    {
        // Huawei-specific implementation
    }

    public function getOnuStatus(OltDevice $olt, int $onuId): array
    {
        // Huawei-specific implementation
    }

    public function configureOnu(OltDevice $olt, array $config): bool
    {
        // Huawei-specific implementation
    }

    public function getOpticalPower(OltDevice $olt, int $onuId): array
    {
        // Huawei-specific implementation
    }
}
```

### Hapi 2: Krijoni Vendor Manager

```php
// src/Services/VendorManager.php
<?php

namespace Botble\FiberhomeOltManager\Services;

use Botble\FiberhomeOltManager\Models\OltDevice;
use Botble\FiberhomeOltManager\Services\Vendors\FiberhomeDriver;
use Botble\FiberhomeOltManager\Services\Vendors\HuaweiDriver;
use Botble\FiberhomeOltManager\Services\Vendors\ZteDriver;

class VendorManager
{
    protected array $drivers = [];

    public function __construct()
    {
        $this->registerDrivers();
    }

    protected function registerDrivers(): void
    {
        $this->drivers = [
            'fiberhome' => FiberhomeDriver::class,
            'huawei' => HuaweiDriver::class,
            'zte' => ZteDriver::class,
        ];
    }

    public function getDriver(OltDevice $olt)
    {
        $vendor = $olt->vendor;
        
        if (!isset($this->drivers[$vendor])) {
            throw new \Exception("Driver not found for vendor: {$vendor}");
        }

        return app($this->drivers[$vendor]);
    }

    public function executeCommand(OltDevice $olt, string $method, array $params = [])
    {
        $driver = $this->getDriver($olt);
        
        if (!method_exists($driver, $method)) {
            throw new \Exception("Method {$method} not supported by {$olt->vendor} driver");
        }

        return $driver->$method($olt, ...$params);
    }
}
```

### Hapi 3: Krijoni Vendor Interface

```php
// src/Services/Vendors/VendorDriverInterface.php
<?php

namespace Botble\FiberhomeOltManager\Services\Vendors;

use Botble\FiberhomeOltManager\Models\OltDevice;

interface VendorDriverInterface
{
    /**
     * Get system information
     */
    public function getSystemInfo(OltDevice $olt): array;

    /**
     * Get list of ONUs
     */
    public function getOnuList(OltDevice $olt): array;

    /**
     * Get ONU status
     */
    public function getOnuStatus(OltDevice $olt, int $onuId): array;

    /**
     * Configure ONU
     */
    public function configureOnu(OltDevice $olt, array $config): bool;

    /**
     * Get optical power
     */
    public function getOpticalPower(OltDevice $olt, int $onuId): array;

    /**
     * Enable ONU
     */
    public function enableOnu(OltDevice $olt, int $onuId): bool;

    /**
     * Disable ONU
     */
    public function disableOnu(OltDevice $olt, int $onuId): bool;

    /**
     * Reboot ONU
     */
    public function rebootOnu(OltDevice $olt, int $onuId): bool;

    /**
     * Create bandwidth profile
     */
    public function createBandwidthProfile(OltDevice $olt, array $profile): bool;

    /**
     * Delete bandwidth profile
     */
    public function deleteBandwidthProfile(OltDevice $olt, int $profileId): bool;
}
```

---

## Huawei OLT OID Mappings

### System Information
```php
'huawei' => [
    'system' => [
        'sysDescr' => '1.3.6.1.2.1.1.1.0',
        'sysUpTime' => '1.3.6.1.2.1.1.3.0',
        'sysName' => '1.3.6.1.2.1.1.5.0',
    ],
    
    'board' => [
        'boardTable' => '1.3.6.1.4.1.2011.6.128.1.1.2.21',
        'boardType' => '1.3.6.1.4.1.2011.6.128.1.1.2.21.1.2',
        'boardStatus' => '1.3.6.1.4.1.2011.6.128.1.1.2.21.1.3',
    ],
    
    'onu' => [
        'onuTable' => '1.3.6.1.4.1.2011.6.128.1.1.2.43',
        'onuStatus' => '1.3.6.1.4.1.2011.6.128.1.1.2.46',
        'onuDistance' => '1.3.6.1.4.1.2011.6.128.1.1.2.53',
        'onuRxPower' => '1.3.6.1.4.1.2011.6.128.1.1.2.51',
        'onuTxPower' => '1.3.6.1.4.1.2011.6.128.1.1.2.52',
    ],
    
    'service' => [
        'serviceProfile' => '1.3.6.1.4.1.2011.6.128.1.1.2.62',
        'lineProfile' => '1.3.6.1.4.1.2011.6.128.1.1.2.63',
    ],
]
```

---

## ZTE OLT OID Mappings

### System Information
```php
'zte' => [
    'system' => [
        'sysDescr' => '1.3.6.1.2.1.1.1.0',
        'sysUpTime' => '1.3.6.1.2.1.1.3.0',
        'sysName' => '1.3.6.1.2.1.1.5.0',
    ],
    
    'card' => [
        'cardTable' => '1.3.6.1.4.1.3902.1012.3.1.1',
        'cardType' => '1.3.6.1.4.1.3902.1012.3.1.1.1.2',
        'cardStatus' => '1.3.6.1.4.1.3902.1012.3.1.1.1.3',
    ],
    
    'onu' => [
        'onuTable' => '1.3.6.1.4.1.3902.1012.3.28.1',
        'onuStatus' => '1.3.6.1.4.1.3902.1012.3.28.1.1.4',
        'onuDistance' => '1.3.6.1.4.1.3902.1012.3.28.2.1.5',
        'onuRxPower' => '1.3.6.1.4.1.3902.1012.3.50.12.1.1.10',
        'onuTxPower' => '1.3.6.1.4.1.3902.1012.3.50.12.1.1.9',
    ],
]
```

---

## Përdorimi

### Shembull 1: Shtimi i Huawei OLT

```php
// Në controller
$olt = OltDevice::create([
    'name' => 'Huawei-OLT-01',
    'vendor' => 'huawei',
    'model' => 'MA5608T',
    'ip_address' => '192.168.1.100',
    'snmp_community' => 'public',
    'snmp_version' => '2c',
]);

// Përdorimi i VendorManager
$vendorManager = app(VendorManager::class);
$systemInfo = $vendorManager->executeCommand($olt, 'getSystemInfo');
$onuList = $vendorManager->executeCommand($olt, 'getOnuList');
```

### Shembull 2: Konfigurimi i ONU në Huawei

```php
$config = [
    'onu_id' => 1,
    'service_profile' => 'PROFILE-100M',
    'line_profile' => 'LINE-PROFILE-1',
    'vlan' => 100,
];

$result = $vendorManager->executeCommand($olt, 'configureOnu', [$config]);
```

---

## Database Seeding për Vendor Configurations

```php
// database/seeders/VendorConfigurationSeeder.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Botble\FiberhomeOltManager\Models\VendorConfiguration;

class VendorConfigurationSeeder extends Seeder
{
    public function run()
    {
        // Fiberhome Configuration
        VendorConfiguration::create([
            'vendor' => 'fiberhome',
            'model' => 'AN5516-01',
            'oid_mappings' => [
                'base' => '1.3.6.1.4.1.5875.800.3',
                // ... OID mappings
            ],
            'capabilities' => [
                'max_onus' => 1024,
                'max_distance' => 20000,
                'supports_qinq' => true,
            ],
        ]);

        // Huawei Configuration
        VendorConfiguration::create([
            'vendor' => 'huawei',
            'model' => 'MA5608T',
            'oid_mappings' => [
                'base' => '1.3.6.1.4.1.2011.6.128',
                // ... OID mappings
            ],
            'capabilities' => [
                'max_onus' => 512,
                'max_distance' => 20000,
                'supports_qinq' => true,
            ],
        ]);

        // ZTE Configuration
        VendorConfiguration::create([
            'vendor' => 'zte',
            'model' => 'C300',
            'oid_mappings' => [
                'base' => '1.3.6.1.4.1.3902.1012',
                // ... OID mappings
            ],
            'capabilities' => [
                'max_onus' => 1024,
                'max_distance' => 20000,
                'supports_qinq' => true,
            ],
        ]);
    }
}
```

---

## Testing

### Unit Test për Huawei Driver

```php
// tests/Unit/HuaweiDriverTest.php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use Botble\FiberhomeOltManager\Services\Vendors\HuaweiDriver;
use Botble\FiberhomeOltManager\Models\OltDevice;

class HuaweiDriverTest extends TestCase
{
    protected HuaweiDriver $driver;
    protected OltDevice $olt;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->driver = new HuaweiDriver();
        $this->olt = OltDevice::factory()->create([
            'vendor' => 'huawei',
            'model' => 'MA5608T',
        ]);
    }

    public function test_can_get_system_info()
    {
        $info = $this->driver->getSystemInfo($this->olt);
        
        $this->assertIsArray($info);
        $this->assertArrayHasKey('model', $info);
        $this->assertArrayHasKey('version', $info);
    }

    public function test_can_get_onu_list()
    {
        $onus = $this->driver->getOnuList($this->olt);
        
        $this->assertIsArray($onus);
    }
}
```

---

## Best Practices

### 1. Vendor Detection

```php
public function detectVendor(string $ipAddress): ?string
{
    $sysDescr = $this->snmp->get($ipAddress, '1.3.6.1.2.1.1.1.0');
    
    if (str_contains($sysDescr, 'Fiberhome')) {
        return 'fiberhome';
    } elseif (str_contains($sysDescr, 'Huawei')) {
        return 'huawei';
    } elseif (str_contains($sysDescr, 'ZTE')) {
        return 'zte';
    }
    
    return null;
}
```

### 2. Error Handling

```php
try {
    $result = $vendorManager->executeCommand($olt, 'getSystemInfo');
} catch (VendorNotSupportedException $e) {
    Log::error("Vendor not supported: " . $e->getMessage());
} catch (MethodNotSupportedException $e) {
    Log::error("Method not supported: " . $e->getMessage());
} catch (\Exception $e) {
    Log::error("Error executing command: " . $e->getMessage());
}
```

### 3. Caching Vendor Configurations

```php
public function getVendorConfig(string $vendor, ?string $model = null)
{
    $cacheKey = "vendor_config_{$vendor}_{$model}";
    
    return Cache::remember($cacheKey, 3600, function () use ($vendor, $model) {
        return VendorConfiguration::where('vendor', $vendor)
            ->where('model', $model)
            ->first();
    });
}
```

---

## Dokumentacioni i Vendor-ave

### Fiberhome
- MIB: GEPON-OLT-COMMON-MIB
- Models: AN5516-01, AN5516-04, AN5516-06
- Documentation: [Fiberhome Docs](https://www.fiberhome.com)

### Huawei
- MIB: HUAWEI-GPON-MIB
- Models: MA5608T, MA5680T, MA5683T
- Documentation: [Huawei Support](https://support.huawei.com)

### ZTE
- MIB: ZTE-AN-GPON-MIB
- Models: C300, C320, C600, C650
- Documentation: [ZTE Support](https://www.zte.com.cn)

---

## Kontributi

Për të shtuar mbështetje për vendor të tjerë:

1. Krijoni një driver të ri që implementon `VendorDriverInterface`
2. Shtoni OID mappings në konfigurimin e vendor-it
3. Testoni me pajisje reale
4. Dokumentoni OID-të dhe funksionalitetet
5. Krijoni pull request

---

**Last Updated**: 2024-01-01  
**Version**: 1.5.0 (Planned)