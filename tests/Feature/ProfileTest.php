<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'phone' => '+2348000000001',
                'alternate_phone' => '+2348000000002',
                'delivery_contact_name' => 'Receiver Person',
                'delivery_phone' => '+2348000000003',
                'delivery_address_line_1' => '10 Example Street',
                'delivery_city' => 'Lagos',
                'delivery_state' => 'Lagos',
                'delivery_country' => 'Nigeria',
                'preferred_payment_method' => 'paystack',
                'billing_name' => 'Test User Billing',
                'billing_email' => 'billing@example.com',
                'billing_phone' => '+2348000000004',
                'billing_address' => '12 Billing Street, Lagos',
                'identity_type' => 'national_id',
                'identity_number' => 'A12345678',
                'identity_country' => 'Nigeria',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('paystack', $user->preferred_payment_method);
        $this->assertSame('10 Example Street', $user->delivery_address_line_1);
        $this->assertSame('national_id', $user->identity_type);
        $this->assertSame('pending', $user->kyc_status);
        $this->assertNotNull($user->kyc_submitted_at);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
