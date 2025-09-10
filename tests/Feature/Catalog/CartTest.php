<?php

namespace Tests\Feature\Catalog;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_index_is_accessible_and_initially_empty(): void
    {
        $response = $this->get(route('cart.index', absolute: false));
        $response->assertOk();
    }

    public function test_cart_state_initially_empty(): void
    {
        $response = $this->get(route('cart.state', absolute: false));
        $response->assertOk()
                 ->assertJson(['ok' => true, 'count' => 0, 'total' => 0]);
    }

    public function test_add_course_adds_item_and_prevents_duplicates(): void
    {
        $course = Course::factory()->create(['is_active' => true, 'price' => 49.99]);

        // Add first time
        $response1 = $this->post(route('cart.add-course', $course, absolute: false));
        $response1->assertRedirect(route('cart.index', absolute: false));

        // Verify state has 1 item
        $this->get(route('cart.state', absolute: false))
            ->assertOk()
            ->assertJson(['ok' => true, 'count' => 1]);

        // Add duplicate
        $response2 = $this->post(route('cart.add-course', $course, absolute: false));
        $response2->assertRedirect(route('cart.index', absolute: false));
        $response2->assertSessionHas('status');

        // Still 1 item
        $this->get(route('cart.state', absolute: false))
            ->assertOk()
            ->assertJson(['ok' => true, 'count' => 1]);
    }

    public function test_add_course_json_returns_item_payload(): void
    {
        $course = Course::factory()->create(['is_active' => true, 'price' => 19.50]);

        $response = $this->postJson(route('cart.add-course', $course, absolute: false));

        $response->assertOk()
                 ->assertJson([
                     'ok' => true,
                     'message' => 'Corso aggiunto al carrello.',
                 ])
                 ->assertJsonStructure([
                     'ok', 'message', 'count', 'item' => ['id','type','course_id','name','price']
                 ]);
    }

    public function test_remove_item_and_clear_cart(): void
    {
        $course = Course::factory()->create(['is_active' => true, 'price' => 29.00]);

        // Add via JSON to get item id
        $add = $this->postJson(route('cart.add-course', $course, absolute: false))
                    ->assertOk()
                    ->json();
        $itemId = $add['item']['id'] ?? null;
        $this->assertNotEmpty($itemId);

        // Remove item
        $this->delete(route('cart.remove', ['id' => $itemId], absolute: false))
            ->assertRedirect(route('cart.index', absolute: false));

        // Cart empty
        $this->get(route('cart.state', absolute: false))
            ->assertOk()
            ->assertJson(['count' => 0]);

        // Add again then clear
        $this->post(route('cart.add-course', $course, absolute: false));
        $this->post(route('cart.clear', absolute: false))
            ->assertRedirect(route('cart.index', absolute: false));

        $this->get(route('cart.state', absolute: false))
            ->assertOk()
            ->assertJson(['count' => 0]);
    }

    public function test_checkout_redirects_to_login_when_not_authenticated(): void
    {
        $course = Course::factory()->create(['is_active' => true, 'price' => 12.00]);
        // Add to cart
        $this->post(route('cart.add-course', $course, absolute: false));

        $response = $this->get(route('cart.checkout', absolute: false));
        $response->assertRedirect(route('login', absolute: false));
        $response->assertSessionHas('status');
    }

    public function test_checkout_with_empty_cart_redirects_with_error(): void
    {
        $response = $this->get(route('cart.checkout', absolute: false));
        $response->assertRedirect(route('cart.index', absolute: false));
        $response->assertSessionHas('error');
    }
}
