<?php

namespace Tests\Feature\Catalog;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\GiftCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_index_is_accessible(): void
    {
        $this->get(route('catalog.index', absolute: false))
            ->assertOk();
    }

    public function test_show_404_for_inactive_course(): void
    {
        $inactive = Course::factory()->inactive()->create();
        $this->get(route('catalog.show', $inactive, absolute: false))
            ->assertNotFound();
    }

    public function test_purchase_redirects_to_course_when_already_enrolled(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['is_active' => true]);
        Enrollment::factory()->for($user)->for($course)->create();

        $this->actingAs($user)
            ->get(route('catalog.checkout', $course, absolute: false))
            ->assertRedirect(route('courses.show', $course, absolute: false));
    }

    public function test_purchase_with_invalid_gift_code_returns_error(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->get(route('catalog.checkout', ['course' => $course->id, 'gift_code' => 'INVALID'], absolute: false))
            ->assertRedirect(route('catalog.show', $course, absolute: false))
            ->assertSessionHas('error');
    }

    public function test_purchase_with_mismatched_gift_code_returns_error(): void
    {
        $user = User::factory()->create();
        $courseA = Course::factory()->create(['is_active' => true]);
        $courseB = Course::factory()->create(['is_active' => true]);

        $gift = GiftCard::create([
            'code' => 'GC-MISMATCH-0001',
            'course_id' => $courseB->id,
            'buyer_user_id' => $user->id,
            'recipient_name' => 'Mario',
            'recipient_email' => 'mario@example.com',
            'message' => 'x',
            'amount' => 1000,
            'currency' => 'eur',
            'status' => 'paid',
            'stripe_session_id' => 'cs_test',
            'stripe_payment_intent_id' => 'pi_test',
            'issued_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('catalog.checkout', ['course' => $courseA->id, 'gift_code' => $gift->code], absolute: false))
            ->assertRedirect(route('catalog.show', $courseA, absolute: false))
            ->assertSessionHas('error');

        $gift->refresh();
        $this->assertSame('paid', $gift->status);
        $this->assertNull($gift->redeemed_at);
    }

    public function test_purchase_with_valid_gift_code_redeems_and_enrolls(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['is_active' => true]);

        $gift = GiftCard::create([
            'code' => 'GC-COURSE-OK01',
            'course_id' => $course->id,
            'buyer_user_id' => $user->id,
            'recipient_name' => 'Mario',
            'recipient_email' => 'mario@example.com',
            'message' => 'x',
            'amount' => 1000,
            'currency' => 'eur',
            'status' => 'paid',
            'stripe_session_id' => 'cs_test',
            'stripe_payment_intent_id' => 'pi_test',
            'issued_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('catalog.checkout', ['course' => $course->id, 'gift_code' => $gift->code], absolute: false))
            ->assertRedirect(route('courses.show', $course, absolute: false))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'is_active' => 1,
        ]);

        $gift->refresh();
        $this->assertSame('redeemed', $gift->status);
        $this->assertNotNull($gift->redeemed_at);
        $this->assertSame($user->id, $gift->redeemer_user_id);
    }
}
