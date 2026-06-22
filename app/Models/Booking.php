<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'booking_type',
        'title',
        'location',
        'check_in_date',
        'check_out_date',
        'travel_date',
        'persons',
        'details',
        'status',
        'payment_status',
        'amount',
        'confirmation_code',
        'confirmation_file',
        'admin_note',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'travel_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function reviews()
    {
        return $this->morphMany(ModuleReview::class, 'reviewable')->latest();
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
