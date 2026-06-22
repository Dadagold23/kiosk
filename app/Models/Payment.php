<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'payment_method',
        'payer_name',
        'payer_email',
        'payer_phone',
        'billing_address',
        'delivery_address_snapshot',
        'customer_profile_snapshot',
        'gateway',
        'reference',
        'gateway_transaction_id',
        'receipt_no',
        'status',
        'gateway_response',
        'paid_at',
        'gateway_verified_at',
        'meta',
    ];

    


    protected $casts = [
        'paid_at' => 'datetime',
        'gateway_verified_at' => 'datetime',
        'meta' => 'array',
        'customer_profile_snapshot' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($payment) {
            if (empty($payment->receipt_no)) {
                $payment->receipt_no = 'RCT-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(5));
            }
        });
    }

    public function payable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_UNDER_REVIEW = 'under_review';
    
}
