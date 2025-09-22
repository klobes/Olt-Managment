<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Port extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'port_number',
        'port_type',
        'status',
        'admin_status',
        'operational_status',
        'speed',
        'duplex',
        'vlan_id',
        'description',
        'rx_power',
        'tx_power',
        'temperature',
        'voltage',
        'current',
        'ont_count',
        'max_ont_count'
    ];

    protected $casts = [
        'status' => 'boolean',
        'admin_status' => 'boolean',
        'operational_status' => 'boolean',
        'rx_power' => 'float',
        'tx_power' => 'float',
        'temperature' => 'float',
        'voltage' => 'float',
        'current' => 'float',
        'ont_count' => 'integer',
        'max_ont_count' => 'integer'
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function onts(): HasMany
    {
        return $this->hasMany(Ont::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('port_type', $type);
    }

    public function isPortFull(): bool
    {
        return $this->ont_count >= $this->max_ont_count;
    }

    public function getUtilizationPercentage(): float
    {
        if ($this->max_ont_count === 0) {
            return 0;
        }
        
        return ($this->ont_count / $this->max_ont_count) * 100;
    }
}