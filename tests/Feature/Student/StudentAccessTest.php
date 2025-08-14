<?php

namespace Tests\Feature\Student;

use App\Models\User;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_to_login_from_courses_index(): void
    {
        $response = $this->get(route('courses.index', absolute: false));

        $response->assertRedirect(route('login'));
    }

    public function test_inactive_student_is_redirected_and_logged_out(): void
    {
        $user = User::factory()->state(['is_active' => false])->create();

        $response = $this->actingAs($user)->get(route('dashboard', absolute: false));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Account disattivato. Contatta il supporto.');
        $this->assertGuest();
    }

    public function test_course_show_forbidden_without_enrollment(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        $response = $this->actingAs($user)->get(route('courses.show', $course, absolute: false));

        $response->assertForbidden();
    }

    public function test_course_show_ok_with_active_enrollment(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        // Create some structure
        $section = Section::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($section)->create();

        Enrollment::factory()->for($user)->for($course)->create();

        $response = $this->actingAs($user)->get(route('courses.show', $course, absolute: false));

        $response->assertOk();
    }

    public function test_lesson_show_404_if_lesson_not_in_course(): void
    {
        $user = User::factory()->create();
        $courseA = Course::factory()->create();
        $courseB = Course::factory()->create();
        $sectionA = Section::factory()->for($courseA)->create();
        $sectionB = Section::factory()->for($courseB)->create();
        $lessonB = Lesson::factory()->for($sectionB)->create();
        Enrollment::factory()->for($user)->for($courseA)->create();

        $response = $this->actingAs($user)->get(route('courses.lesson', ['course' => $courseA->id, 'lesson' => $lessonB->id], absolute: false));

        $response->assertNotFound();
    }

    public function test_lesson_show_ok_with_active_enrollment(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $section = Section::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($section)->create();
        Enrollment::factory()->for($user)->for($course)->create();

        $response = $this->actingAs($user)->get(route('courses.lesson', ['course' => $course->id, 'lesson' => $lesson->id], absolute: false));

        $response->assertOk();
    }
}
