<?php

namespace Database\Factories;

use App\Enums\CourseOfferingStatus;
use App\Models\AcademicSemester;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Lecturer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseOffering>
 */
class CourseOfferingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $semester = AcademicSemester::inRandomOrder()->firstOrFail();
        return [
            'course_id' => $this->faker->randomElement(Course::pluck('id')),
            'semester_id' => $semester->id,
            'lecturer_id' => $this->faker->randomElement(Lecturer::pluck('id')),
            'max_students' => fake()->numberBetween(30,60),
            'status' => $this->faker->randomElement(CourseOfferingStatus::cases()),
            'start_date' => $semester->start_date,
            'end_date' => $semester->end_date,
        ];
    }
}
