<?php

namespace Database\Factories;

use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\Department;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->sentence(3);

        return [
            'name' => ucwords($name),
            'description' => fake()->sentence(8),
            'department_id' => $this->faker->randomElement(Department::pluck('id')),
            'credit_value' => fake()->numberBetween(1,3)*100,
            'course_level' => fake()->numberBetween(1,5),
            'status' => $this->faker->randomElement(CourseStatus::cases()),
        ];
    }
}
