<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterSubscriptionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $subscription = NewsletterSubscription::query()->firstOrNew([
            'email' => strtolower($validated['email']),
        ]);

        $wasActive = $subscription->exists && $subscription->status === 'active';

        $subscription->fill([
            'source' => 'footer',
            'status' => 'active',
            'subscribed_at' => $subscription->subscribed_at ?? now(),
            'unsubscribed_at' => null,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        $subscription->save();

        return back()->with(
            'newsletter_status',
            $wasActive
                ? 'This email is already subscribed to Kiosk updates.'
                : 'You have been subscribed to Kiosk updates successfully.'
        );
    }
}
