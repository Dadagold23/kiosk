<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ServiceTrackingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_service_tracking_update(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = User::factory()->create();
        Role::findOrCreate('Admin', 'web');
        $admin->assignRole('Admin');

        $customer = User::factory()->create();
        $category = Category::create([
            'name' => 'Electrical',
            'slug' => 'electrical',
            'type' => 'service',
            'status' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'user_id' => $customer->id,
            'category_id' => $category->id,
            'title' => 'Home Electrical Fix',
            'description' => 'Repair living room sockets.',
            'location' => 'Lekki, Lagos',
            'status' => 'pending',
            'progress_status' => 'request_received',
            'payment_status' => 'pending',
            'fee' => 5000,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.services.track', $serviceRequest), [
            'progress_status' => 'team_assigned',
            'assigned_team' => 'Field Ops Team A',
            'location' => 'Ikeja dispatch desk',
            'next_step' => 'Technician will call customer before arrival',
            'tracking_note' => 'Technician assigned and preparing for site visit.',
            'event_time' => now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $serviceRequest->refresh();

        $this->assertSame('team_assigned', $serviceRequest->progress_status);
        $this->assertSame('approved', $serviceRequest->status);
        $this->assertSame('Field Ops Team A', $serviceRequest->assigned_team);
        $this->assertDatabaseHas('service_tracking_events', [
            'service_request_id' => $serviceRequest->id,
            'status' => 'team_assigned',
            'location' => 'Ikeja dispatch desk',
        ]);
    }

    public function test_customer_can_view_service_tracking_timeline(): void
    {
        $customer = User::factory()->create();
        $category = Category::create([
            'name' => 'Plumbing',
            'slug' => 'plumbing',
            'type' => 'service',
            'status' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'user_id' => $customer->id,
            'category_id' => $category->id,
            'title' => 'Kitchen Plumbing',
            'description' => 'Leak inspection and repair.',
            'location' => 'Yaba, Lagos',
            'status' => 'in_progress',
            'progress_status' => 'on_site',
            'payment_status' => 'paid',
            'fee' => 5000,
        ]);

        $serviceRequest->trackingEvents()->create([
            'status' => 'on_site',
            'location' => 'Customer apartment',
            'next_step' => 'Inspection underway',
            'note' => 'Technician has arrived and started diagnostics.',
            'event_time' => now(),
        ]);

        $response = $this->actingAs($customer)->get(route('customer.services.show', $serviceRequest));

        $response->assertOk();
        $response->assertSee('Tracking Timeline');
        $response->assertSee('On site', false);
        $response->assertSee('Inspection underway');
    }
}
