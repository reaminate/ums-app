<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Grade>
 */
class GradeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $student = Student::findOrFail($this->faker->randomElement(Student::pluck('id')));
        return [
            'student_id' => $student->id,
            'course_offering_id' => $student->courseOfferings,
            'total_assignment_score' => fake()->numberBetween(0,40),
            'total_test_marks' => fake()->numberBetween(0,60),
        ];
    }
}
