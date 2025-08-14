<?php

namespace Tests\Feature\Student;

use App\Models\User;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentProgressTest extends TestCase
{
    use RefreshDatabase;

    // Using default exception handling to allow JSON error bodies to be returned and dumped

    public function test_update_progress_forbidden_without_enrollment(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $section = Section::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($section)->create();

        $payload = [
            'lesson_id' => $lesson->id,
            'watch_time_seconds' => 120,
            'progress_percentage' => 50,
            'completed' => false,
        ];

        $response = $this->actingAs($user)
            ->postJson(route('progress.update'), $payload);

        $response->assertStatus(403)
                 ->assertJson(['error' => 'Non autorizzato']);
    }

    public function test_update_progress_success_with_active_enrollment(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $section = Section::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($section)->create();
        Enrollment::factory()->for($user)->for($course)->create([
            'expires_at' => null,
            'is_active' => true,
        ]);

        $payload = [
            'lesson_id' => $lesson->id,
            'watch_time_seconds' => 120,
            'progress_percentage' => 50,
            'completed' => false,
        ];

        // Debug pre-conditions: enrollment must be active and not expired
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
        $this->assertNotNull($enrollment, 'Enrollment not found before calling API');
        $this->assertFalse($enrollment->isExpired(), 'Enrollment is expired unexpectedly: '.optional($enrollment->expires_at)->toIso8601String());

        $response = $this->actingAs($user)
            ->postJson(route('progress.update'), $payload);

        $response->assertOk()
                 ->assertJson(['success' => true]);

        // Verify lesson progress via model to avoid decimal formatting issues on sqlite
        $progress = LessonProgress::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();
        $this->assertNotNull($progress);
        $this->assertSame(120, (int) $progress->watch_time_seconds);
        $this->assertEquals(50.0, (float) $progress->progress_percentage);
        $this->assertFalse((bool) $progress->completed);

        // Enrollment progress should remain 0 since no lesson is marked completed
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
        $this->assertNotNull($enrollment);
        $this->assertEquals(0.0, (float) $enrollment->progress_percentage);
    }

    public function test_mark_as_completed_updates_enrollment_progress(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $section = Section::factory()->for($course)->create();
        $lesson1 = Lesson::factory()->for($section)->create();
        $lesson2 = Lesson::factory()->for($section)->create();
        Enrollment::factory()->for($user)->for($course)->create([
            'expires_at' => null,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('progress.complete'), [
                'lesson_id' => $lesson1->id,
            ]);

        $response->assertOk()
                 ->assertJson(['success' => true]);

        $progress = LessonProgress::where('user_id', $user->id)
            ->where('lesson_id', $lesson1->id)
            ->first();
        $this->assertNotNull($progress);
        $this->assertTrue((bool) $progress->completed);
        $this->assertEquals(100.0, (float) $progress->progress_percentage);

        // 1 lesson completed out of 2 => 50%
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
        $this->assertNotNull($enrollment);
        $this->assertEquals(50.0, (float) $enrollment->progress_percentage);
    }

    public function test_update_progress_forbidden_when_enrollment_expired(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $section = Section::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($section)->create();
        Enrollment::factory()->for($user)->for($course)->expired()->create();

        $payload = [
            'lesson_id' => $lesson->id,
            'watch_time_seconds' => 60,
            'progress_percentage' => 30,
            'completed' => false,
        ];

        $response = $this->actingAs($user)
            ->postJson(route('progress.update'), $payload);

        $response->assertStatus(403)
                 ->assertJson(['error' => 'Non autorizzato']);
    }
}
