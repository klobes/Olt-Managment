<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ont extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'device_id',
        'port_id',
        'ont_id',
        'serial_number',
        'mac_address',
        'model',
        'firmware_version',
        'status',
        'admin_status',
        'operational_status',
        'rx_power',
        'tx_power',
        'distance',
        'last_seen_at',
        'customer_info',
        'service_profile',
        'vlan_config',
        'bandwidth_profile'
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'status' => 'boolean',
        'admin_status' => 'boolean',
        'operational_status' => 'boolean',
        'rx_power' => 'float',
        'tx_power' => 'float',
        'distance' => 'float',
        'customer_info' => 'array',
        'service_profile' => 'array',
        'vlan_config' => 'array',
        'bandwidth_profile' => 'array'
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function port(): BelongsTo
    {
        return $this->belongsTo(Port::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOnline($query)
    {
        return $query->where('operational_status', true);
    }

    public function isOnline(): bool
    {
        return $this->operational_status && 
               $this->last_seen_at?->diffInMinutes(now()) < 10;
    }

    public function getSignalQuality(): string
    {
        if ($this->rx_power === null) {
            return 'unknown';
        }

        return match(true) {
            $this->rx_power >= -20 => 'excellent',
            $this->rx_power >= -25 => 'good',
            $this->rx_power >= -28 => 'fair',
            default => 'poor'
        };
    }
}