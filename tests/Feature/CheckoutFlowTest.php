<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderTrackingEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'kiosk.payments.paystack.public_key' => 'pk_test_123',
            'kiosk.payments.paystack.secret_key' => 'sk_test_123',
            'kiosk.payments.paystack.base_url' => 'https://api.paystack.co',
        ]);
    }

    public function test_checkout_initializes_paystack_and_clears_cart(): void
    {
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'message' => 'Authorization URL created',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/mock-session',
                    'access_code' => 'mock_access_code',
                    'reference' => 'mock_reference',
                ],
            ], 200),
        ]);

        $user = User::factory()->create([
            'address' => '10 Example Street, Lagos',
            'email' => 'customer@examplemail.com',
            'delivery_contact_name' => 'Test Receiver',
            'delivery_phone' => '+2348000000010',
            'delivery_address_line_1' => '10 Example Street',
            'delivery_city' => 'Lagos',
            'delivery_state' => 'Lagos',
            'delivery_country' => 'Nigeria',
            'preferred_payment_method' => 'paystack',
            'billing_name' => 'Test User Billing',
            'billing_email' => 'billing@examplemail.com',
            'billing_phone' => '+2348000000011',
            'billing_address' => '12 Billing Street, Lagos',
            'identity_type' => 'national_id',
            'identity_country' => 'Nigeria',
            'kyc_status' => 'pending',
        ]);

        $product = Product::create([
            'name' => 'Premium Kiosk Item',
            'price' => 15000,
            'quantity' => 20,
            'status' => true,
            'source_type' => 'local',
        ]);

        $this->actingAs($user)->post(route('cart.store', $product));

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'delivery_address' => '10 Example Street, Lagos',
            'notes' => 'Please call on arrival.',
        ]);

        $order = Order::with('payments')->first();

        $this->assertNotNull($order);
        $response->assertRedirect('https://checkout.paystack.com/mock-session');
        $this->assertSame(Payment::STATUS_PENDING, $order->payment_status);
        $this->assertSame(1, $order->payments->count());
        $this->assertSame('paystack', $order->payments->first()->payment_method);
        $this->assertSame($order->payment_reference, $order->payments->first()->reference);
        $this->assertSame('Test User Billing', $order->payments->first()->payer_name);
        $this->assertSame('billing@examplemail.com', $order->payments->first()->payer_email);
        $this->assertSame('10 Example Street, Lagos', $order->payments->first()->delivery_address_snapshot);
        $this->assertSame('pending', data_get($order->payments->first()->customer_profile_snapshot, 'kyc_status'));
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_checkout_rejects_out_of_stock_cart_items(): void
    {
        $user = User::factory()->create([
            'address' => '10 Example Street, Lagos',
            'email' => 'stock@examplemail.com',
        ]);

        $product = Product::create([
            'name' => 'Limited Item',
            'price' => 5000,
            'quantity' => 1,
            'status' => true,
            'source_type' => 'local',
        ]);

        $this->actingAs($user)->post(route('cart.store', $product));

        $cart = Cart::where('user_id', $user->id)->firstOrFail();
        $cart->items()->first()->update([
            'qty' => 3,
            'subtotal' => 15000,
        ]);

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'delivery_address' => '10 Example Street, Lagos',
            'notes' => 'Please call on arrival.',
        ]);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_checkout_allows_global_sourcing_items_with_zero_stock_quantity(): void
    {
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'message' => 'Authorization URL created',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/mock-global-session',
                    'access_code' => 'mock_global_access_code',
                    'reference' => 'mock_global_reference',
                ],
            ], 200),
        ]);

        $user = User::factory()->create([
            'address' => '10 Example Street, Lagos',
            'email' => 'global@examplemail.com',
        ]);

        $product = Product::create([
            'name' => 'Global Sourced Backpack',
            'price' => 42000,
            'quantity' => 0,
            'status' => true,
            'source_type' => 'global',
            'source_marketplace' => 'temu',
        ]);

        $this->actingAs($user)->post(route('cart.store', $product));

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'delivery_address' => '10 Example Street, Lagos',
            'notes' => 'Global order checkout.',
        ]);

        $response->assertRedirect('https://checkout.paystack.com/mock-global-session');
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('payments', 1);
    }

    public function test_paystack_callback_verifies_payment_and_redirects_back_to_shop(): void
    {
        Http::fake([
            'https://api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'message' => 'Verification successful',
                'data' => [
                    'id' => 99001,
                    'status' => 'success',
                    'amount' => 1800000,
                    'currency' => 'NGN',
                    'gateway_response' => 'Successful',
                    'paid_at' => now()->toIso8601String(),
                    'reference' => 'PAY-CALLBACK-001',
                ],
            ], 200),
        ]);

        $user = User::factory()->create([
            'email' => 'callback@examplemail.com',
        ]);
        $order = Order::create([
            'user_id' => $user->id,
            'order_no' => 'KSK-CALLBACK-001',
            'order_type' => 'local_shop',
            'subtotal' => 15000,
            'delivery_fee' => 2500,
            'service_charge' => 500,
            'total' => 18000,
            'payment_status' => Payment::STATUS_PENDING,
            'order_status' => Order::STATUS_PENDING,
            'payment_reference' => 'PAY-CALLBACK-001',
            'delivery_address' => '10 Example Street, Lagos',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_name' => 'Premium Kiosk Item',
            'qty' => 1,
            'unit_price' => 15000,
            'subtotal' => 15000,
            'fulfillment_status' => 'pending',
        ]);

        $payment = $order->payments()->create([
            'user_id' => $user->id,
            'amount' => 18000,
            'currency' => 'NGN',
            'payment_method' => 'paystack',
            'gateway' => 'paystack',
            'reference' => 'PAY-CALLBACK-001',
            'status' => Payment::STATUS_PENDING,
        ]);

        $response = $this->get(route('payments.paystack.callback', ['reference' => $payment->reference]));

        $response->assertRedirect(route('shop.index'));
        $response->assertSessionHas('success');

        $payment->refresh();
        $order->refresh();

        $this->assertSame(Payment::STATUS_PAID, $payment->status);
        $this->assertSame(Payment::STATUS_PAID, $order->payment_status);
        $this->assertSame(Order::STATUS_PROCESSING, $order->order_status);
    }

    public function test_paystack_webhook_marks_payment_paid(): void
    {
        $secret = 'sk_test_123';
        config(['kiosk.payments.paystack.secret_key' => $secret]);

        $user = User::factory()->create([
            'email' => 'webhook@examplemail.com',
        ]);
        $order = Order::create([
            'user_id' => $user->id,
            'order_no' => 'KSK-WEBHOOK-001',
            'order_type' => 'local_shop',
            'subtotal' => 12000,
            'delivery_fee' => 2500,
            'service_charge' => 500,
            'total' => 15000,
            'payment_status' => Payment::STATUS_PENDING,
            'order_status' => Order::STATUS_PENDING,
            'payment_reference' => 'PAY-WEBHOOK-001',
            'delivery_address' => 'Example Street',
        ]);

        $payment = $order->payments()->create([
            'user_id' => $user->id,
            'amount' => 15000,
            'currency' => 'NGN',
            'payment_method' => 'paystack',
            'gateway' => 'paystack',
            'reference' => 'PAY-WEBHOOK-001',
            'status' => Payment::STATUS_PENDING,
        ]);

        $payload = json_encode([
            'event' => 'charge.success',
            'data' => [
                'id' => 99123,
                'status' => 'success',
                'amount' => 1500000,
                'currency' => 'NGN',
                'gateway_response' => 'Successful',
                'paid_at' => now()->toIso8601String(),
                'reference' => $payment->reference,
            ],
        ], JSON_THROW_ON_ERROR);

        $signature = hash_hmac('sha512', $payload, $secret);

        $response = $this->call(
            'POST',
            route('payments.paystack.webhook'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_PAYSTACK_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertOk();
        $payment->refresh();
        $order->refresh();

        $this->assertSame(Payment::STATUS_PAID, $payment->status);
        $this->assertSame(Payment::STATUS_PAID, $order->payment_status);
    }

    public function test_checkout_redirects_to_profile_when_customer_email_is_not_paystack_compatible(): void
    {
        $user = User::factory()->create([
            'address' => '10 Example Street, Lagos',
            'email' => 'customer@kiosk.test',
        ]);

        $product = Product::create([
            'name' => 'Premium Kiosk Item',
            'price' => 15000,
            'quantity' => 20,
            'status' => true,
            'source_type' => 'local',
        ]);

        $this->actingAs($user)->post(route('cart.store', $product));

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'delivery_address' => '10 Example Street, Lagos',
            'notes' => 'Please call on arrival.',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_admin_order_item_tracking_supports_procurement_stage_updates(): void
    {
        $admin = User::factory()->create();
        \Spatie\Permission\Models\Role::findOrCreate('Admin', 'web');
        $admin->assignRole('Admin');

        $customer = User::factory()->create();
        $order = Order::create([
            'user_id' => $customer->id,
            'order_no' => 'KSK-PROC-001',
            'order_type' => 'global_shop',
            'subtotal' => 10000,
            'delivery_fee' => 1000,
            'service_charge' => 500,
            'total' => 11500,
            'payment_status' => Payment::STATUS_PAID,
            'order_status' => Order::STATUS_PENDING,
            'delivery_address' => '12 Broad Street, Lagos',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_name' => 'Tracked Item',
            'qty' => 1,
            'unit_price' => 10000,
            'subtotal' => 10000,
            'fulfillment_status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.orders.items.update', [$order, $item]), [
            'fulfillment_status' => 'procurement_in_progress',
            'location' => 'Overseas supplier desk',
            'event_note' => 'Supplier purchase order has been raised.',
        ]);

        $response->assertRedirect();
        $item->refresh();
        $order->refresh();

        $this->assertSame('procurement_in_progress', $item->fulfillment_status);
        $this->assertSame(Order::STATUS_PROCESSING, $order->order_status);
        $this->assertDatabaseHas('order_tracking_events', [
            'order_item_id' => $item->id,
            'status' => 'procurement_in_progress',
            'location' => 'Overseas supplier desk',
        ]);
    }
}
