<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Course;
use App\Models\WorkoutCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Permission;

class WorkoutCardBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_builder_requires_admin_auth(): void
    {
        $course = Course::factory()->create(['is_active' => true]);
        $response = $this->get(route('admin.workout-cards.builder', $course, absolute: false));
        $response->assertStatus(302);
        $location = $response->headers->get('Location');
        $this->assertIsString($location);
        $this->assertStringContainsString('/admin/login', $location);
    }

    public function test_builder_renders_with_course(): void
    {
        $admin = Admin::factory()->create();
        $course = Course::factory()->create(['is_active' => true]);

        // Ensure permission exists and is granted to admin
        Permission::firstOrCreate(['name' => 'workout-cards.manage', 'guard_name' => 'admin']);
        $admin->givePermissionTo('workout-cards.manage');

        $this->actingAs($admin, 'admin')
            ->get(route('admin.workout-cards.builder', $course, absolute: false))
            ->assertOk();
    }

    public function test_store_from_builder_creates_workout_card(): void
    {
        $admin = Admin::factory()->create();
        $course = Course::factory()->create(['is_active' => true]);

        // Ensure permission exists and is granted to admin
        Permission::firstOrCreate(['name' => 'workout-cards.manage', 'guard_name' => 'admin']);
        $admin->givePermissionTo('workout-cards.manage');

        $payload = [
            'course_id' => $course->id,
            'card_title' => 'Scheda Test',
            'header_logo_url' => 'https://example.com/logo.svg',
            'info_box_scadenza' => '1 mese',
            'info_box_check' => '3 foto inizio/fine',
            'workouts' => [
                [
                    'title' => 'Workout A',
                    'warmup' => '10 min camminata',
                    'venous_return' => 'Stretching gambe',
                    'exercises' => [
                        ['name' => 'Squat', 'series' => '3x', 'reps' => '10', 'rest' => '60s', 'note' => ''],
                        ['name' => 'Pushup', 'series' => '3x', 'reps' => '12', 'rest' => '60s', 'note' => ''],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.workout-cards.store-builder', absolute: false), $payload);

        $response->assertRedirect(route('admin.workout-cards.builder', $course));
        $response->assertSessionHas('success');

        $card = WorkoutCard::where('course_id', $course->id)->first();
        $this->assertNotNull($card);
        $this->assertSame('Scheda Test', $card->title);
        $this->assertTrue($card->is_active);
        $this->assertIsString($card->content);
        $this->assertStringContainsString('SCADENZA PROGRAMMA', $card->content);
        // Il titolo del workout resta quello passato (es. "Workout A"), non viene forzato in maiuscolo
        $this->assertStringContainsString('Workout A', $card->content);
        $this->assertStringContainsString('ESERCIZIO', $card->content);
        $this->assertStringContainsString('RISCALDAMENTO', $card->content);
    }

    public function test_store_from_builder_updates_existing_workout_card(): void
    {
        $admin = Admin::factory()->create();
        $course = Course::factory()->create(['is_active' => true]);
        $existing = WorkoutCard::factory()->create([
            'course_id' => $course->id,
            'title' => 'Vecchia Scheda',
        ]);

        // Ensure permission exists and is granted to admin
        Permission::firstOrCreate(['name' => 'workout-cards.manage', 'guard_name' => 'admin']);
        $admin->givePermissionTo('workout-cards.manage');

        $payload = [
            'course_id' => $course->id,
            'card_title' => 'Nuova Scheda',
            'info_box_scadenza' => '2 mesi',
            'info_box_check' => 'Foto ogni 2 settimane',
            'workouts' => [
                [
                    'title' => 'Workout B',
                    'warmup' => 'Bike 5min',
                    'venous_return' => 'Respirazione',
                    'exercises' => [
                        ['name' => 'Rematore', 'series' => '4x', 'reps' => '8', 'rest' => '90s', 'note' => 'Lento'],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.workout-cards.store-builder', absolute: false), $payload);

        $response->assertRedirect(route('admin.workout-cards.builder', $course, absolute: false));
        $response->assertSessionHas('success');

        $existing->refresh();
        $this->assertSame('Nuova Scheda', $existing->title);
        $this->assertStringContainsString('Workout B', $existing->content);
        $this->assertStringContainsString('Rematore', $existing->content);
    }
}
