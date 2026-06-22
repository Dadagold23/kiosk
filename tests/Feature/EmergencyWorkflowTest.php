<?php

namespace Tests\Feature;

use App\Models\EmergencyRequest;
use App\Models\EmergencyServiceUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class EmergencyWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_emergency_page_renders_structured_geo_and_directory_sections(): void
    {
        $response = $this->get(route('emergency.index'));

        $response->assertOk();
        $response->assertSee('Official Emergency Lines');
        $response->assertSee('Units In Selected State');
    }

    public function test_authenticated_user_can_submit_emergency_request_with_geo_fields(): void
    {
        $user = User::factory()->create([
            'phone' => '08030000010',
        ]);

        $response = $this->actingAs($user)->post(route('emergency.store'), [
            'country_code' => 'NG',
            'country_name' => 'Nigeria',
            'emergency_type' => 'medical',
            'full_name' => 'Sample Customer',
            'phone' => '08030000010',
            'alternate_phone' => '08030000999',
            'state_code' => 'LAGOS',
            'state_name' => 'Lagos',
            'local_government_area' => 'Eti Osa',
            'location_text' => 'Lekki Phase 1, Admiralty Way',
            'latitude' => '6.4474000',
            'longitude' => '3.4698000',
            'description' => 'Medical emergency near Admiralty Way.',
        ]);

        $response->assertRedirect(route('emergency.index'));

        $this->assertDatabaseHas('emergency_requests', [
            'user_id' => $user->id,
            'country_code' => 'NG',
            'state_name' => 'Lagos',
            'local_government_area' => 'Eti Osa',
            'location_text' => 'Lekki Phase 1, Admiralty Way',
        ]);
    }

    public function test_admin_can_assign_unit_and_add_tracking_update(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $customer = User::factory()->create();
        $deskOfficer = User::factory()->create();
        Role::findOrCreate('Emergency Desk', 'web');
        $deskOfficer->assignRole('Emergency Desk');

        $unit = EmergencyServiceUnit::create([
            'country_code' => 'NG',
            'state_name' => 'Lagos',
            'unit_code' => 'RS2.1',
            'service_type' => 'road_safety_sector_command',
            'unit_name' => 'Federal Road Safety Corps Lagos Sector Command',
            'contact_phone' => '08077690201',
            'contact_email' => 'rs2.1.lagos@frsc.gov.ng',
            'toll_free_line' => '122',
            'address' => 'Ojodu-Isheri Road, Ikeja, Lagos',
            'website' => 'https://frsc.gov.ng/commands/sector-commands/',
            'source_url' => 'https://frsc.gov.ng/commands/sector-commands/',
            'is_national' => false,
            'coverage_scope' => 'state',
        ]);

        $request = EmergencyRequest::create([
            'user_id' => $customer->id,
            'country_code' => 'NG',
            'country_name' => 'Nigeria',
            'emergency_type' => 'accident',
            'full_name' => 'Incident Caller',
            'phone' => '08030000010',
            'state_name' => 'Lagos',
            'local_government_area' => 'Eti Osa',
            'location_text' => 'Lekki Toll Gate',
            'description' => 'Road traffic accident.',
            'status' => EmergencyRequest::STATUS_PENDING,
        ]);

        $this->actingAs($deskOfficer)->post(route('admin.emergency.update', $request), [
            'status' => EmergencyRequest::STATUS_RESPONDING,
            'assigned_unit_id' => $unit->id,
            'response_note' => 'Unit assigned for dispatch.',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('emergency_requests', [
            'id' => $request->id,
            'assigned_unit_id' => $unit->id,
            'assigned_unit_contact' => '08077690201',
        ]);

        $this->actingAs($deskOfficer)->post(route('admin.emergency.track', $request), [
            'status' => 'en_route',
            'emergency_service_unit_id' => $unit->id,
            'location_label' => 'Lekki-Ikoyi Link Bridge',
            'latitude' => '6.4698000',
            'longitude' => '3.5000000',
            'eta_minutes' => 8,
            'note' => 'Unit is moving toward the destination.',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('emergency_tracking_events', [
            'emergency_request_id' => $request->id,
            'emergency_service_unit_id' => $unit->id,
            'status' => 'en_route',
            'location_label' => 'Lekki-Ikoyi Link Bridge',
        ]);
    }

    public function test_owner_can_fetch_tracking_payload(): void
    {
        $customer = User::factory()->create();
        $unit = EmergencyServiceUnit::create([
            'country_code' => 'NG',
            'state_name' => 'Lagos',
            'unit_code' => 'RS2.1',
            'service_type' => 'road_safety_sector_command',
            'unit_name' => 'Federal Road Safety Corps Lagos Sector Command',
            'contact_phone' => '08077690201',
            'toll_free_line' => '122',
            'is_national' => false,
            'coverage_scope' => 'state',
        ]);

        $request = EmergencyRequest::create([
            'user_id' => $customer->id,
            'country_code' => 'NG',
            'country_name' => 'Nigeria',
            'emergency_type' => 'medical',
            'full_name' => 'Incident Caller',
            'phone' => '08030000010',
            'state_name' => 'Lagos',
            'local_government_area' => 'Eti Osa',
            'location_text' => 'Lekki Phase 1',
            'latitude' => 6.4474000,
            'longitude' => 3.4698000,
            'description' => 'Medical emergency.',
            'status' => EmergencyRequest::STATUS_RESPONDING,
            'assigned_unit_id' => $unit->id,
            'assigned_unit' => $unit->unit_name,
            'assigned_unit_contact' => $unit->contact_phone,
            'assigned_unit_toll_free' => $unit->toll_free_line,
        ]);

        $request->trackingEvents()->create([
            'emergency_service_unit_id' => $unit->id,
            'status' => 'en_route',
            'location_label' => 'Third Mainland Bridge corridor',
            'latitude' => 6.5244000,
            'longitude' => 3.3792000,
            'eta_minutes' => 10,
            'note' => 'Unit is approaching the Lekki corridor.',
            'event_time' => now(),
        ]);

        $this->actingAs($customer)
            ->get(route('customer.emergency.tracking', $request))
            ->assertOk()
            ->assertJsonFragment([
                'assigned_unit' => $unit->unit_name,
                'location_label' => 'Third Mainland Bridge corridor',
            ]);
    }
}
