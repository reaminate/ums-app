<?php

namespace Database\Factories;

use App\Models\AcademicProgram;
use App\Models\Course;
use App\Models\CourseProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseProgram>
 */
class CourseProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => $this->faker->randomElement(Course::pluck('id')),
            'academic_program_id' => $this->faker->randomElement(AcademicProgram::pluck('id')),
        ];
    }
}
