<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }

    public function test_admin_can_authenticate_using_the_login_screen(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->post('/admin/login', [
            'username' => $admin->username,
            'password' => 'password',
        ]);

        $this->assertAuthenticated('admin');
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_admin_cannot_authenticate_with_invalid_password(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->from(route('admin.login'))
            ->post('/admin/login', [
                'username' => $admin->username,
                'password' => 'wrong-password',
            ]);

        $this->assertGuest('admin');
        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHasErrors('username');
    }

    public function test_admin_can_logout(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post('/admin/logout');

        $this->assertGuest('admin');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_inactive_admin_is_redirected_by_middleware(): void
    {
        $admin = Admin::factory()->inactive()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHas('error', 'Account disattivato.');
        $this->assertGuest('admin');
    }
}
