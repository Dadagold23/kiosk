<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ServiceRequest extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'location',
        'preferred_date',
        'budget',
        'images',
        'assigned_to',
        'assigned_team',
        'status',
        'progress_status',
        'tracking_updated_at',
        'service_window_start',
        'service_window_end',
        'completed_at',
        'payment_status',
        'fee',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'images' => 'array',
        'budget' => 'decimal:2',
        'fee' => 'decimal:2',
        'tracking_updated_at' => 'datetime',
        'service_window_start' => 'datetime',
        'service_window_end' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function assignedStaff()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function trackingEvents()
    {
        return $this->hasMany(ServiceTrackingEvent::class)->latest('event_time');
    }

    public function reviews()
    {
        return $this->morphMany(ModuleReview::class, 'reviewable')->latest();
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    public const STATUS_PENDING = 'pending';
    public const STATUS_REVIEWING = 'reviewing';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SOURCED = 'sourced';
    public const STATUS_DISPATCHED = 'dispatched';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const TRACKING_REQUEST_RECEIVED = 'request_received';
    public const TRACKING_PAYMENT_CONFIRMED = 'payment_confirmed';
    public const TRACKING_UNDER_REVIEW = 'under_review';
    public const TRACKING_TEAM_ASSIGNED = 'team_assigned';
    public const TRACKING_VISIT_SCHEDULED = 'visit_scheduled';
    public const TRACKING_EN_ROUTE = 'en_route';
    public const TRACKING_ON_SITE = 'on_site';
    public const TRACKING_IN_PROGRESS = 'in_progress';
    public const TRACKING_AWAITING_PARTS = 'awaiting_parts';
    public const TRACKING_QUALITY_CHECK = 'quality_check';
    public const TRACKING_COMPLETED = 'completed';
    public const TRACKING_CLOSED = 'closed';

    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';

    public function getImageUrlsAttribute(): array
    {
        return collect($this->images ?? [])
            ->filter(fn ($image) => is_string($image) && $image !== '' && Storage::disk('public')->exists($image))
            ->map(fn ($image) => asset('storage/' . $image))
            ->values()
            ->all();
    }

}
