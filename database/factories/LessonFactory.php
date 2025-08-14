<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition(): array
    {
        return [
            'section_id' => Section::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'video_url' => fake()->url(),
            'duration_minutes' => fake()->numberBetween(3, 60),
            'lesson_order' => fake()->numberBetween(1, 50),
            'is_active' => true,
            'video_metadata' => [],
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
