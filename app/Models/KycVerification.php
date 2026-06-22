<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycVerification extends Model
{
    protected $fillable = [
        'user_id',
        'checked_by',
        'provider',
        'environment',
        'status',
        'identity_type',
        'identity_number_masked',
        'identity_country',
        'provider_reference',
        'request_payload',
        'response_payload',
        'notes',
        'verified_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}
