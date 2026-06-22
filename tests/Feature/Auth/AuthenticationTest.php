<?php

namespace Tests\Feature\Auth;

use App\Http\Middleware\EnforceIdleLogout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_admin_users_are_redirected_to_the_admin_dashboard_after_login(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $user = User::factory()->create();
        Role::findOrCreate('Admin', 'web');
        $user->assignRole('Admin');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_admin_dashboard_renders_for_admin_users(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $user = User::factory()->create();
        Role::findOrCreate('Admin', 'web');
        $user->assignRole('Admin');

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
        $response->assertSee('Recent Orders');
        $response->assertSee('Revenue Logged');
    }

    public function test_authenticated_admin_users_are_redirected_away_from_the_login_screen_to_their_admin_home(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $user = User::factory()->create();
        Role::findOrCreate('Admin', 'web');
        $user->assignRole('Admin');

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_admin_users_are_redirected_away_from_the_customer_dashboard(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $user = User::factory()->create();
        Role::findOrCreate('Admin', 'web');
        $user->assignRole('Admin');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_idle_sessions_are_logged_out_after_thirty_minutes_of_inactivity(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->withSession([
                EnforceIdleLogout::SESSION_KEY => now()->subMinutes(31)->getTimestamp(),
            ])
            ->get('/dashboard');

        $response->assertRedirect(route('login', absolute: false));
        $response->assertSessionHas('status', 'You were signed out after 30 minutes of inactivity.');
        $this->assertGuest();
    }

    public function test_authenticated_users_can_touch_their_session_activity_timestamp(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('session.activity', absolute: false));

        $response->assertNoContent();
        $this->assertNotNull(session(EnforceIdleLogout::SESSION_KEY));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
