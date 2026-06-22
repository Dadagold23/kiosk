<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'item_name',
        'source_type',
        'source_marketplace',
        'qty',
        'unit_price',
        'subtotal',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrlAttribute(): string
    {
        $image = data_get($this->meta, 'image');

        if (is_string($image) && $image !== '' && Storage::disk('public')->exists($image)) {
            return asset('storage/' . $image);
        }

        if ($this->relationLoaded('product') ? $this->product : $this->product()->exists()) {
            return $this->product?->image_url ?? asset(config('kiosk.assets.product_placeholder'));
        }

        return asset(config('kiosk.assets.product_placeholder'));
    }
}
