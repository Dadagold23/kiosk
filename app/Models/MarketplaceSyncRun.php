<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceSyncRun extends Model
{
    protected $fillable = [
        'provider',
        'source',
        'status',
        'feed_url',
        'items_seen',
        'items_created',
        'items_updated',
        'items_deactivated',
        'started_at',
        'finished_at',
        'error_message',
        'meta',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'meta' => 'array',
    ];
}
