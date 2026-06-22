<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceTrackingEvent extends Model
{
    protected $fillable = [
        'service_request_id',
        'status',
        'location',
        'next_step',
        'note',
        'event_time',
        'meta',
    ];

    protected $casts = [
        'event_time' => 'datetime',
        'meta' => 'array',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }
}
