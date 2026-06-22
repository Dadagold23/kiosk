<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyServiceUnit extends Model
{
    protected $fillable = [
        'country_code',
        'state_name',
        'unit_code',
        'service_type',
        'unit_name',
        'contact_phone',
        'contact_email',
        'toll_free_line',
        'address',
        'website',
        'source_url',
        'is_national',
        'coverage_scope',
        'meta',
    ];

    protected $casts = [
        'is_national' => 'boolean',
        'meta' => 'array',
    ];

    public function emergencyRequests()
    {
        return $this->hasMany(EmergencyRequest::class, 'assigned_unit_id');
    }

    public function trackingEvents()
    {
        return $this->hasMany(EmergencyTrackingEvent::class);
    }
}
