<?php

namespace Database\Factories;

use App\Models\WorkoutCard;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkoutCard>
 */
class WorkoutCardFactory extends Factory
{
    protected $model = WorkoutCard::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => fake()->sentence(3),
            'content' => fake()->paragraph(),
            'warmup' => fake()->sentence(),
            'venous_return' => fake()->sentence(),
            'notes' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
