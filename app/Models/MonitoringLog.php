<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitoringLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'metric_type',
        'metric_name',
        'value',
        'unit',
        'threshold_min',
        'threshold_max',
        'status',
        'alert_level',
        'message',
        'recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'value' => 'float',
        'threshold_min' => 'float',
        'threshold_max' => 'float'
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function scopeAlerts($query)
    {
        return $query->whereIn('alert_level', ['warning', 'critical']);
    }

    public function scopeByMetricType($query, $type)
    {
        return $query->where('metric_type', $type);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('recorded_at', '>=', now()->subHours($hours));
    }

    public function isThresholdExceeded(): bool
    {
        if ($this->threshold_min !== null && $this->value < $this->threshold_min) {
            return true;
        }

        if ($this->threshold_max !== null && $this->value > $this->threshold_max) {
            return true;
        }

        return false;
    }
}