<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CoursePrerequisite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CoursePrerequisite>
 */
class CoursePrerequisiteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $course = Course::where('course_level', '>', 1)->inRandomOrder()->first();
        $course_prerequisite = $this->faker->optional(0.7)->randomElement(
            Course::where('course_level', '<', $course->course_level)->pluck('id')
        );

        return [
            'course_id' => $course->id,
            'prerequisite_id' => $course_prerequisite,
        ];
    }
}
