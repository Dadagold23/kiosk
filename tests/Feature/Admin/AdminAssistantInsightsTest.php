<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminAssistantInsightsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_displays_assistant_panel(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = User::factory()->create();
        Role::findOrCreate('Admin', 'web');
        $admin->assignRole('Admin');

        $customer = User::factory()->create();
        $order = Order::create([
            'user_id' => $customer->id,
            'order_no' => 'KSK-AI-001',
            'order_type' => 'global_shop',
            'subtotal' => 25000,
            'delivery_fee' => 2500,
            'service_charge' => 500,
            'total' => 28000,
            'payment_status' => 'paid',
            'order_status' => 'processing',
            'delivery_address' => '12 Example Street, Lagos',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_name' => 'Assistant Tracked Item',
            'qty' => 1,
            'unit_price' => 25000,
            'subtotal' => 25000,
            'fulfillment_status' => 'procurement_in_progress',
            'last_tracked_at' => now()->subHours(30),
        ]);

        $category = Category::create([
            'name' => 'Technical Support',
            'slug' => 'technical-support',
            'type' => 'service',
            'status' => true,
        ]);

        ServiceRequest::create([
            'user_id' => $customer->id,
            'category_id' => $category->id,
            'title' => 'Generator servicing',
            'description' => 'Routine maintenance',
            'location' => 'Abuja',
            'status' => 'reviewing',
            'progress_status' => 'awaiting_parts',
            'payment_status' => 'paid',
            'fee' => 5000,
            'tracking_updated_at' => now()->subHours(20),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Admin Assistant');
        $response->assertSee('ETA + Risk Monitor');
    }

    public function test_order_and_service_detail_pages_display_admin_assistant(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = User::factory()->create();
        Role::findOrCreate('Admin', 'web');
        $admin->assignRole('Admin');

        $customer = User::factory()->create();
        $order = Order::create([
            'user_id' => $customer->id,
            'order_no' => 'KSK-AI-002',
            'order_type' => 'local_shop',
            'subtotal' => 10000,
            'delivery_fee' => 1000,
            'service_charge' => 500,
            'total' => 11500,
            'payment_status' => 'paid',
            'order_status' => 'processing',
            'delivery_address' => 'Yaba, Lagos',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_name' => 'On-route item',
            'qty' => 1,
            'unit_price' => 10000,
            'subtotal' => 10000,
            'fulfillment_status' => 'in_transit',
            'last_tracked_at' => now()->subHours(2),
        ]);

        $category = Category::create([
            'name' => 'Repairs',
            'slug' => 'repairs',
            'type' => 'service',
            'status' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'user_id' => $customer->id,
            'category_id' => $category->id,
            'title' => 'Air conditioner repair',
            'description' => 'Cooling issue',
            'location' => 'Ikeja',
            'status' => 'in_progress',
            'progress_status' => 'en_route',
            'payment_status' => 'paid',
            'fee' => 5000,
            'tracking_updated_at' => now()->subHours(1),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Admin Assistant')
            ->assertSee('Estimated Completion');

        $this->actingAs($admin)
            ->get(route('admin.services.show', $serviceRequest))
            ->assertOk()
            ->assertSee('Admin Assistant')
            ->assertSee('Estimated Completion');
    }
}
