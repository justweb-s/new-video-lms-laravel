<?php

namespace Tests\Feature\GiftCard;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GiftCardPurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_giftcards_index_is_accessible(): void
    {
        $response = $this->get(route('giftcards.index', absolute: false));
        $response->assertOk();
    }

    public function test_giftcards_show_404_when_course_inactive_or_free(): void
    {
        $inactive = Course::factory()->inactive()->create(['price' => 100]);
        $free = Course::factory()->create(['is_active' => true, 'price' => 0]);

        $this->get(route('giftcards.show', $inactive, absolute: false))->assertNotFound();
        $this->get(route('giftcards.show', $free, absolute: false))->assertNotFound();
    }

    public function test_checkout_redirects_to_login_when_guest_get(): void
    {
        $course = Course::factory()->create(['is_active' => true, 'price' => 50]);

        $response = $this->get(route('giftcards.checkout', $course, absolute: false));
        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_checkout_guest_post_stores_form_data_and_sets_intended_url(): void
    {
        $course = Course::factory()->create(['is_active' => true, 'price' => 50]);

        $payload = [
            'recipient_name' => 'Mario Rossi',
            'recipient_email' => 'mario@example.com',
            'message' => 'Auguri!',
        ];

        $response = $this->from(route('giftcards.show', $course, absolute: false))
            ->post(route('giftcards.checkout', $course, absolute: false), $payload);

        $response->assertRedirect(route('login', absolute: false));
        $response->assertSessionHas('gift_checkout_'.$course->id, $payload);
        // Il controller salva l'URL intended in forma assoluta
        $response->assertSessionHas('url.intended', route('giftcards.checkout', $course, absolute: true));
    }

    public function test_checkout_authenticated_get_without_saved_data_redirects_back_with_error(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['is_active' => true, 'price' => 50]);

        $response = $this->actingAs($user)
            ->get(route('giftcards.checkout', $course, absolute: false));

        $response->assertRedirect(route('giftcards.show', $course, absolute: false));
        $response->assertSessionHas('error');
    }
}
