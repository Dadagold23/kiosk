<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceProvider extends Model
{
    protected $fillable = [
        'provider_key',
        'label',
        'enabled',
        'feed_url',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];
}
