<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminManagementPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_management_pages_render_successfully(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $user = User::factory()->create();
        Role::findOrCreate('Admin', 'web');
        $user->assignRole('Admin');

        $this->actingAs($user)->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('Products');

        $this->actingAs($user)->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('Categories');

        $this->actingAs($user)->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee('Orders');

        $this->actingAs($user)->get(route('admin.activity-logs.index'))
            ->assertOk()
            ->assertSee('Activity Logs');

        $this->actingAs($user)->get(route('admin.reports.index'))
            ->assertOk()
            ->assertSee('Reports & Analytics');

        $this->actingAs($user)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Admin Assistant');
    }
}
