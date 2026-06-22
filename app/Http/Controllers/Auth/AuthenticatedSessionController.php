<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Middleware\EnforceIdleLogout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        $request->session()->put(EnforceIdleLogout::SESSION_KEY, now()->getTimestamp());

        /** @var \App\Models\User $user */
        $user = $request->user();

        return redirect()->intended($user->homePath());
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($request->boolean('idle_logout')) {
            return redirect()
                ->route('login')
                ->with('status', 'You were signed out after 30 minutes of inactivity.');
        }

        return redirect('/');
    }

    public function touch(Request $request): Response
    {
        $request->session()->put(EnforceIdleLogout::SESSION_KEY, now()->getTimestamp());

        return response()->noContent();
    }
}
