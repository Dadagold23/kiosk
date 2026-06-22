<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleReview extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'rating',
        'title',
        'review',
        'would_recommend',
        'show_identity',
        'public_name',
        'status',
        'is_featured',
        'moderation_note',
        'moderated_by',
        'moderated_at',
    ];

    protected $casts = [
        'would_recommend' => 'boolean',
        'show_identity' => 'boolean',
        'is_featured' => 'boolean',
        'moderated_at' => 'datetime',
    ];

    public function reviewable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function publicReviewerName(): string
    {
        if (! $this->show_identity) {
            return 'Verified customer';
        }

        return $this->public_name ?: $this->user?->name ?: 'Verified customer';
    }

    public function moduleLabel(): string
    {
        return match ($this->reviewable_type) {
            Order::class => 'Orders',
            ServiceRequest::class => 'Services',
            ConsultancyRequest::class => 'Consultancy',
            Booking::class => 'Booking',
            EmergencyRequest::class => 'Emergency',
            default => 'Kiosk',
        };
    }

    public function canBeModeratedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return match ($this->reviewable_type) {
            Order::class => $user->hasAnyRole(['Super Admin', 'Admin', 'Shop Manager']),
            ServiceRequest::class => $user->hasAnyRole(['Super Admin', 'Admin', 'Service Manager']),
            ConsultancyRequest::class => $user->hasAnyRole(['Super Admin', 'Admin', 'Consultant Manager']),
            Booking::class => $user->hasAnyRole(['Super Admin', 'Admin', 'Booking Manager']),
            EmergencyRequest::class => $user->hasAnyRole(['Super Admin', 'Admin', 'Emergency Desk']),
            default => false,
        };
    }
}
