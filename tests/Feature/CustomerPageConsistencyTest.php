<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerPageConsistencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cart_shows_added_items(): void
    {
        $user = User::factory()->create();

        $product = Product::create([
            'name' => 'Visible Cart Product',
            'price' => 12500,
            'quantity' => 10,
            'status' => true,
            'source_type' => 'local',
        ]);

        $response = $this->actingAs($user)->post(route('cart.store', $product));

        $response->assertRedirect(route('cart.index'));
        $this->assertDatabaseHas('cart_items', [
            'item_name' => 'Visible Cart Product',
            'qty' => 1,
        ]);

        $this->actingAs($user)
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Visible Cart Product')
            ->assertSee('Proceed to Checkout');
    }

    public function test_customer_module_pages_render_inside_shared_dashboard_shell(): void
    {
        $user = User::factory()->create();

        foreach ([
            route('cart.index'),
            route('customer.services.index'),
            route('customer.services.create'),
            route('customer.consultancy.index'),
            route('customer.consultancy.create'),
            route('customer.bookings.index'),
            route('customer.bookings.create'),
            route('notifications.index'),
        ] as $path) {
            $this->actingAs($user)
                ->get($path)
                ->assertOk()
                ->assertSee('Kiosk Account');
        }
    }
}
