<?php

namespace Botble\FiberhomeOltManager\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OltDevice extends BaseModel
{
    protected $table = 'olt_devices';

    protected $fillable = [
        'name',
        'ip_address',
        'snmp_community',
        'snmp_version',
        'snmp_port',
        'location',
        'description',
        'status',
        'last_seen',
        'system_info',
        'is_active',
    ];

    protected $casts = [
        'system_info' => 'array',
        'is_active' => 'boolean',
        'last_seen' => 'datetime',
    ];

    public function cards(): HasMany
    {
        return $this->hasMany(OltCard::class);
    }

    public function ponPorts(): HasMany
    {
        return $this->hasMany(OltPonPort::class);
    }

    public function onus(): HasMany
    {
        return $this->hasMany(Onu::class);
    }

    public function bandwidthProfiles(): HasMany
    {
        return $this->hasMany(BandwidthProfile::class);
    }

    public function performanceLogs(): HasMany
    {
        return $this->hasMany(OltPerformanceLog::class);
    }

    public function isOnline(): bool
    {
        return $this->status === 'online';
    }

    public function getSnmpConnectionString(): string
    {
        return sprintf(
            '%s:%s@%s:%d',
            $this->snmp_version,
            $this->snmp_community,
            $this->ip_address,
            $this->snmp_port
        );
    }
}