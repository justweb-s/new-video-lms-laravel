<?php

namespace Tests\Feature\GiftCard;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\GiftCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GiftCardRedeemTest extends TestCase
{
    use RefreshDatabase;

    public function test_redeem_form_page_is_accessible(): void
    {
        // Page accessible without code
        $this->get(route('giftcards.redeem', absolute: false))
            ->assertOk();

        // Page accessible with code parameter
        $this->get(route('giftcards.redeem', ['code' => 'GC-FOO-BAR'], absolute: false))
            ->assertOk();
    }

    public function test_redeem_requires_auth(): void
    {
        $response = $this->post(route('giftcards.redeem.submit', absolute: false), [
            'code' => 'GC-INVALID',
        ]);

        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_redeem_invalid_code_shows_error(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('giftcards.redeem', absolute: false))
            ->post(route('giftcards.redeem.submit', absolute: false), [
                'code' => 'GC-INVALID',
            ]);

        $response->assertRedirect(route('giftcards.redeem', absolute: false));
        $response->assertSessionHas('error');
    }

    public function test_redeem_valid_code_creates_enrollment_and_marks_gift_as_redeemed(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create([
            'is_active' => true,
            'price' => 100.00,
        ]);

        $gift = GiftCard::create([
            'code' => 'GC-TEST-1234',
            'course_id' => $course->id,
            'buyer_user_id' => $user->id, // il buyer può essere chiunque, non influisce
            'recipient_name' => 'Mario Rossi',
            'recipient_email' => 'mario@example.com',
            'message' => 'Buon corso!',
            'amount' => 10000,
            'currency' => 'eur',
            'status' => 'paid',
            'stripe_session_id' => 'cs_test_123',
            'stripe_payment_intent_id' => 'pi_test_123',
            'issued_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->post(route('giftcards.redeem.submit', absolute: false), [
                'code' => $gift->code,
            ]);

        $response->assertRedirect(route('catalog.show', $course, absolute: false));
        $response->assertSessionHas('status');

        // Verifica iscrizione creata/attivata
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'is_active' => 1,
        ]);

        // Verifica gift card marcata come redeemed
        $gift->refresh();
        $this->assertSame('redeemed', $gift->status);
        $this->assertNotNull($gift->redeemed_at);
        $this->assertSame($user->id, $gift->redeemer_user_id);
    }
}
