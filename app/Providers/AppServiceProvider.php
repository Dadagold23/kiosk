<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Category;
use App\Models\ConsultancyRequest;
use App\Models\EmergencyRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Policies\ActivityLogPolicy;
use App\Policies\BookingPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ConsultancyRequestPolicy;
use App\Policies\EmergencyRequestPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ServiceRequestPolicy;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ((bool) env('APP_FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(ActivityLog::class, ActivityLogPolicy::class);
        Gate::policy(ServiceRequest::class, ServiceRequestPolicy::class);
        Gate::policy(ConsultancyRequest::class, ConsultancyRequestPolicy::class);
        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(EmergencyRequest::class, EmergencyRequestPolicy::class);
    }
}
