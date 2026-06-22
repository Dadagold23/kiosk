<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTrackingEvent extends Model
{
    protected $fillable = [
        'order_item_id',
        'status',
        'location',
        'note',
        'event_time',
        'meta',
    ];

    protected $casts = [
        'event_time' => 'datetime',
        'meta' => 'array',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
