<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceIdleLogout
{
    public const SESSION_KEY = 'security.last_activity_at';

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return $next($request);
        }

        $timeoutMinutes = max(1, (int) config('kiosk.security.idle_timeout_minutes', 30));
        $timeoutSeconds = $timeoutMinutes * 60;
        $now = now()->getTimestamp();
        $lastActivityAt = (int) $request->session()->get(self::SESSION_KEY, $now);

        if (($now - $lastActivityAt) >= $timeoutSeconds) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('status', "You were signed out after {$timeoutMinutes} minutes of inactivity.");
        }

        $request->session()->put(self::SESSION_KEY, $now);

        return $next($request);
    }
}
