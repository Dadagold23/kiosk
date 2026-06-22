<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'qty',
        'unit_price',
        'subtotal',
        'fulfillment_status',
        'logistics_partner',
        'tracking_number',
        'tracking_url',
        'last_tracked_at',
        'shipped_at',
        'delivered_at',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'last_tracked_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function trackingEvents()
    {
        return $this->hasMany(OrderTrackingEvent::class)->latest('event_time');
    }

    public function getImageUrlAttribute(): string
    {
        return $this->product?->image_url
            ?? asset(config('kiosk.assets.product_placeholder'));
    }

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCUREMENT_REVIEW = 'procurement_review';
    public const STATUS_SUPPLIER_CONFIRMED = 'supplier_confirmed';
    public const STATUS_PROCUREMENT_IN_PROGRESS = 'procurement_in_progress';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SOURCED = 'sourced';
    public const STATUS_QUALITY_CHECK = 'quality_check';
    public const STATUS_PACKED = 'packed';
    public const STATUS_READY_FOR_DISPATCH = 'ready_for_dispatch';
    public const STATUS_DISPATCHED = 'dispatched';
    public const STATUS_IN_TRANSIT = 'in_transit';
    public const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_FAILED_DELIVERY = 'failed_delivery';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_CANCELLED = 'cancelled';
}
