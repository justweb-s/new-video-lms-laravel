<?php

namespace Tests\Feature\Student;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\WorkoutCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentWorkoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_workout_forbidden_without_enrollment(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        $this->actingAs($user)
            ->get(route('courses.workout', $course, absolute: false))
            ->assertStatus(403);
    }

    public function test_workout_not_found_when_no_workout_card(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        Enrollment::factory()->for($user)->for($course)->create();

        $this->actingAs($user)
            ->get(route('courses.workout', $course, absolute: false))
            ->assertNotFound();
    }

    public function test_workout_not_found_when_workout_card_inactive(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        Enrollment::factory()->for($user)->for($course)->create();
        WorkoutCard::factory()->inactive()->create(['course_id' => $course->id]);

        $this->actingAs($user)
            ->get(route('courses.workout', $course, absolute: false))
            ->assertNotFound();
    }

    public function test_workout_ok_with_active_workout_card_and_enrollment(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        Enrollment::factory()->for($user)->for($course)->create();
        WorkoutCard::factory()->create(['course_id' => $course->id, 'is_active' => true]);

        $this->actingAs($user)
            ->get(route('courses.workout', $course, absolute: false))
            ->assertOk();
    }
}
