<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultancyRequest extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'subject',
        'description',
        'preferred_date',
        'assigned_consultant_id',
        'status',
        'payment_status',
        'fee',
        'report_file',
        'admin_note',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'fee' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function assignedConsultant()
    {
        return $this->belongsTo(User::class, 'assigned_consultant_id');
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
        return 'id';
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
