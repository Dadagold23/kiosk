<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyRequest extends Model
{
    protected $fillable = [
        'user_id',
        'country_code',
        'country_name',
        'emergency_type',
        'full_name',
        'phone',
        'alternate_phone',
        'location_text',
        'state_code',
        'state_name',
        'local_government_area',
        'latitude',
        'longitude',
        'description',
        'status',
        'assigned_unit',
        'assigned_unit_id',
        'assigned_unit_contact',
        'assigned_unit_toll_free',
        'dispatch_reference',
        'assigned_at',
        'last_tracked_at',
        'resolved_at',
        'response_note',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'assigned_at' => 'datetime',
        'last_tracked_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUnit()
    {
        return $this->belongsTo(EmergencyServiceUnit::class, 'assigned_unit_id');
    }

    public function trackingEvents()
    {
        return $this->hasMany(EmergencyTrackingEvent::class)->orderByDesc('event_time')->orderByDesc('id');
    }

    public function latestTrackingEvent()
    {
        return $this->hasOne(EmergencyTrackingEvent::class)->latestOfMany('event_time');
    }

    public function reviews()
    {
        return $this->morphMany(ModuleReview::class, 'reviewable')->latest();
    }

    public const STATUS_PENDING = 'pending';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_RESPONDING = 'responding';
    public const STATUS_ON_SCENE = 'on_scene';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';
}
