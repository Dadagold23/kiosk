<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyTrackingEvent extends Model
{
    protected $fillable = [
        'emergency_request_id',
        'emergency_service_unit_id',
        'status',
        'location_label',
        'latitude',
        'longitude',
        'eta_minutes',
        'note',
        'event_time',
        'meta',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'eta_minutes' => 'integer',
        'event_time' => 'datetime',
        'meta' => 'array',
    ];

    public function emergencyRequest()
    {
        return $this->belongsTo(EmergencyRequest::class);
    }

    public function emergencyServiceUnit()
    {
        return $this->belongsTo(EmergencyServiceUnit::class);
    }
}
