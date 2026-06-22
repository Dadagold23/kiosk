<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_no',
        'order_type',
        'subtotal',
        'delivery_fee',
        'service_charge',
        'total',
        'payment_status',
        'order_status',
        'payment_reference',
        'delivery_address',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function latestTrackingEvents()
    {
        return $this->hasManyThrough(OrderTrackingEvent::class, OrderItem::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function reviews()
    {
        return $this->morphMany(ModuleReview::class, 'reviewable')->latest();
    }

    public function getRouteKeyName(): string
    {
        return 'order_no';
    }
    
    public const STATUS_PENDING = 'pending';
    public const STATUS_REVIEWING = 'reviewing';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SOURCED = 'sourced';
    public const STATUS_DISPATCHED = 'dispatched';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';

}
