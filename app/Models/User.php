<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable; 
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'alternate_phone',
        'address',
        'date_of_birth',
        'gender',
        'nationality',
        'country',
        'state',
        'local_government_area',
        'city',
        'postal_code',
        'delivery_contact_name',
        'delivery_phone',
        'delivery_address_line_1',
        'delivery_address_line_2',
        'delivery_city',
        'delivery_state',
        'delivery_local_government_area',
        'delivery_postal_code',
        'delivery_country',
        'delivery_landmark',
        'preferred_payment_method',
        'billing_name',
        'billing_email',
        'billing_phone',
        'billing_address',
        'kyc_status',
        'kyc_submitted_at',
        'kyc_approved_at',
        'identity_type',
        'identity_number',
        'identity_country',
        'preferred_country_code',
        'preferred_language',
        'cookie_consent_mode',
        'cookie_consent_preferences',
        'cookie_consent_set_at',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'kyc_submitted_at' => 'datetime',
            'kyc_approved_at' => 'datetime',
            'cookie_consent_preferences' => 'array',
            'cookie_consent_set_at' => 'datetime',
        ];
    }

    public function deliveryAddress(): ?string
    {
        $parts = array_filter([
            $this->delivery_address_line_1,
            $this->delivery_address_line_2,
            $this->delivery_landmark,
            $this->delivery_city,
            $this->delivery_state,
            $this->delivery_local_government_area,
            $this->delivery_postal_code,
            $this->delivery_country,
        ]);

        return $parts !== [] ? implode(', ', $parts) : $this->address;
    }

    public function billingAddressForPayment(): ?string
    {
        return $this->billing_address ?: $this->deliveryAddress() ?: $this->address;
    }
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function consultancyRequests()
    {
        return $this->hasMany(ConsultancyRequest::class);
    }

    public function assignedServiceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'assigned_to');
    }

    public function assignedConsultancyRequests()
    {
        return $this->hasMany(ConsultancyRequest::class, 'assigned_consultant_id');
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function emergencyRequests()
    {
        return $this->hasMany(EmergencyRequest::class);
    }

    public function moduleReviews()
    {
        return $this->hasMany(ModuleReview::class);
    }

    public function kycVerifications()
    {
        return $this->hasMany(KycVerification::class)->latest();
    }

    public function homeRouteName(): string
    {
        if ($this->hasAnyRole(['Super Admin', 'Admin'])) {
            return 'admin.dashboard';
        }

        if ($this->hasRole('Shop Manager')) {
            return 'admin.orders.index';
        }

        if ($this->hasRole('Service Manager')) {
            return 'admin.services.index';
        }

        if ($this->hasRole('Consultant Manager')) {
            return 'admin.consultancy.index';
        }

        if ($this->hasRole('Booking Manager')) {
            return 'admin.bookings.index';
        }

        if ($this->hasRole('Emergency Desk')) {
            return 'admin.emergency.index';
        }

        return 'dashboard';
    }

    public function homePath(array $query = []): string
    {
        $path = route($this->homeRouteName());

        if ($query === []) {
            return $path;
        }

        return $path.(str_contains($path, '?') ? '&' : '?').http_build_query($query);
    }

    public function hasBackOfficeHome(): bool
    {
        return $this->homeRouteName() !== 'dashboard';
    }

    public function profilePhotoUrl(): string
    {
        if (filled($this->profile_photo_path)) {
            return Storage::disk('public')->url($this->profile_photo_path);
        }

        return asset(config('kiosk.assets.meta_image'));
    }
}
