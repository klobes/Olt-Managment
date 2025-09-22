<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'vendor',
        'model',
        'ip_address',
        'snmp_community',
        'snmp_version',
        'username',
        'password',
        'location',
        'description',
        'status',
        'last_seen_at',
        'firmware_version',
        'serial_number',
        'mac_address',
        'port_count',
        'configuration'
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'configuration' => 'array',
        'status' => 'boolean'
    ];

    protected $hidden = [
        'password',
        'snmp_community'
    ];

    public function ports(): HasMany
    {
        return $this->hasMany(Port::class);
    }

    public function onts(): HasMany
    {
        return $this->hasMany(Ont::class);
    }

    public function monitoringLogs(): HasMany
    {
        return $this->hasMany(MonitoringLog::class);
    }

    public function scopeByVendor($query, $vendor)
    {
        return $query->where('vendor', $vendor);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function isOnline(): bool
    {
        return $this->status && $this->last_seen_at?->diffInMinutes(now()) < 5;
    }

    public function getVendorAdapterClass(): string
    {
        return match($this->vendor) {
            'fiberhome' => \App\Services\Vendors\FiberhomeAdapter::class,
            'huawei' => \App\Services\Vendors\HuaweiAdapter::class,
            'zte' => \App\Services\Vendors\ZteAdapter::class,
            default => \App\Services\Vendors\GenericAdapter::class
        };
    }
}