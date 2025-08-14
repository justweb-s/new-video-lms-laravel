<?php

namespace Database\Factories;

use App\Models\LessonProgress;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LessonProgress>
 */
class LessonProgressFactory extends Factory
{
    protected $model = LessonProgress::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'lesson_id' => Lesson::factory(),
            'completed' => false,
            'completed_at' => null,
            'watch_time_seconds' => fake()->numberBetween(0, 600),
            'progress_percentage' => fake()->numberBetween(0, 100),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'completed' => true,
            'completed_at' => now(),
            'progress_percentage' => 100,
        ]);
    }
}
