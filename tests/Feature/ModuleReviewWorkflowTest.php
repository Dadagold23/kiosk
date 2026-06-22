<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\EmergencyRequest;
use App\Models\ModuleReview;
use App\Models\Order;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ModuleReviewWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_submit_order_review_and_admin_can_approve_it(): void
    {
        $customer = User::factory()->create([
            'email' => 'reviewer@examplemail.com',
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'order_no' => 'KSK-REVIEW-001',
            'order_type' => 'local_shop',
            'subtotal' => 20000,
            'delivery_fee' => 2500,
            'service_charge' => 500,
            'total' => 23000,
            'payment_status' => 'paid',
            'order_status' => 'delivered',
            'delivery_address' => '10 Admiralty Way, Lagos',
        ]);

        $response = $this->actingAs($customer)->post(route('reviews.store', [
            'type' => 'order',
            'record' => $order->order_no,
        ]), [
            'rating' => 5,
            'title' => 'Delivered exactly as promised',
            'review' => 'Delivered exactly as promised, with good communication and a smooth drop-off process for the order.',
            'would_recommend' => '1',
            'show_identity' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('module_reviews', [
            'user_id' => $customer->id,
            'reviewable_type' => Order::class,
            'reviewable_id' => $order->id,
            'status' => ModuleReview::STATUS_PENDING,
        ]);

        $admin = User::factory()->create();
        Role::findOrCreate('Admin', 'web');
        $admin->assignRole('Admin');

        $review = ModuleReview::firstOrFail();

        $approvalResponse = $this->actingAs($admin)->post(route('admin.reviews.moderate', $review), [
            'status' => ModuleReview::STATUS_APPROVED,
            'is_featured' => '1',
            'moderation_note' => 'Approved for storefront display.',
        ]);

        $approvalResponse->assertRedirect();
        $this->assertDatabaseHas('module_reviews', [
            'id' => $review->id,
            'status' => ModuleReview::STATUS_APPROVED,
            'is_featured' => true,
        ]);

        $this->get(route('shop.index'))
            ->assertOk()
            ->assertSee('Delivered exactly as promised')
            ->assertSee('Approved feedback from customers whose orders were delivered successfully.');
    }

    public function test_customer_cannot_submit_review_for_unresolved_emergency_request(): void
    {
        $customer = User::factory()->create();

        $emergencyRequest = EmergencyRequest::create([
            'user_id' => $customer->id,
            'country_code' => 'NG',
            'country_name' => 'Nigeria',
            'emergency_type' => 'medical',
            'full_name' => $customer->name,
            'phone' => '08030000000',
            'location_text' => 'Lekki, Lagos',
            'state_name' => 'Lagos',
            'local_government_area' => 'Eti Osa',
            'description' => 'Emergency review eligibility test.',
            'status' => EmergencyRequest::STATUS_RESPONDING,
        ]);

        $response = $this->actingAs($customer)->post(route('reviews.store', [
            'type' => 'emergency',
            'record' => $emergencyRequest->getRouteKey(),
        ]), [
            'rating' => 4,
            'title' => 'Too early to review',
            'review' => 'This request is still in progress, so the review should not be accepted at this stage at all.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('module_reviews', 0);
    }

    public function test_services_page_only_shows_approved_reviews(): void
    {
        $category = Category::create([
            'name' => 'Electrical Repairs',
            'slug' => 'electrical-repairs',
            'type' => 'service',
            'status' => true,
        ]);

        $customer = User::factory()->create();

        $approvedRequest = ServiceRequest::create([
            'user_id' => $customer->id,
            'category_id' => $category->id,
            'title' => 'Generator rewiring',
            'description' => 'Approved review service request.',
            'status' => 'completed',
            'progress_status' => ServiceRequest::TRACKING_COMPLETED,
            'payment_status' => 'paid',
            'fee' => 5000,
        ]);

        $pendingRequest = ServiceRequest::create([
            'user_id' => $customer->id,
            'category_id' => $category->id,
            'title' => 'Pending moderation request',
            'description' => 'Pending review service request.',
            'status' => 'completed',
            'progress_status' => ServiceRequest::TRACKING_COMPLETED,
            'payment_status' => 'paid',
            'fee' => 5000,
        ]);

        $approvedRequest->reviews()->create([
            'user_id' => $customer->id,
            'rating' => 5,
            'title' => 'Exceptional technician coordination',
            'review' => 'Exceptional technician coordination and clear updates throughout the job from start to finish.',
            'would_recommend' => true,
            'show_identity' => true,
            'public_name' => $customer->name,
            'status' => ModuleReview::STATUS_APPROVED,
            'is_featured' => true,
        ]);

        $pendingRequest->reviews()->create([
            'user_id' => $customer->id,
            'rating' => 3,
            'title' => 'Still pending moderation',
            'review' => 'This text should remain hidden until an administrator explicitly approves the review.',
            'would_recommend' => false,
            'show_identity' => true,
            'public_name' => $customer->name,
            'status' => ModuleReview::STATUS_PENDING,
        ]);

        $this->get(route('services.index'))
            ->assertOk()
            ->assertSee('Exceptional technician coordination')
            ->assertDontSee('Still pending moderation');
    }
}
