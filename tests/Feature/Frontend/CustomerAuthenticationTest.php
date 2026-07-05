<?php

namespace Tests\Feature\Frontend;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get('/cuenta/registro')->assertStatus(200);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get('/cuenta/login')->assertStatus(200);
    }

    public function test_customer_can_register(): void
    {
        $response = $this->post('/cuenta/registro', [
            'name' => 'Cliente Prueba',
            'email' => 'cliente@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertAuthenticatedAs(Customer::first(), 'customer');
        $response->assertRedirect(route('cuenta.dashboard'));
    }

    public function test_customer_can_authenticate_using_the_login_screen(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->post('/cuenta/login', [
            'email' => $customer->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($customer, 'customer');
        $response->assertRedirect(route('cuenta.dashboard'));
    }

    public function test_customer_can_not_authenticate_with_invalid_password(): void
    {
        $customer = Customer::factory()->create();

        $this->post('/cuenta/login', [
            'email' => $customer->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest('customer');
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/cuenta/panel')->assertRedirect(route('cuenta.login'));
    }

    public function test_authenticated_customer_can_view_dashboard(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($customer, 'customer')
            ->get('/cuenta/panel')
            ->assertStatus(200)
            ->assertSee($customer->name);
    }

    public function test_customer_can_logout(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer, 'customer')->post('/cuenta/logout');

        $this->assertGuest('customer');
        $response->assertRedirect(route('home'));
    }

    public function test_customer_session_cannot_access_admin_panel(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer, 'customer')->get('/admin');

        $response->assertRedirect('/login');
        $this->assertGuest('web');
    }

    public function test_staff_session_cannot_access_customer_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'web')->get('/cuenta/panel');

        $response->assertRedirect(route('cuenta.login'));
        $this->assertGuest('customer');
    }
}
