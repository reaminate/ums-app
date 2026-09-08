<?php

namespace Database\Factories;

use App\Models\CourseOffering;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exam>
 */
class ExamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_offering_id' => $this->faker->randomElement(CourseOffering::pluck('id')),
            'exam_type' => $this->faker->randomElement(['mid_sem', 'quiz', 'final']),
            'exam_date' => fake()->date(),
            'max_marks' => fake()->numberBetween(40, 100),
            'weight' => fake()->numberBetween(40,60),
        ];
    }
}
