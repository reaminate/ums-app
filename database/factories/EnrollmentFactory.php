<?php

namespace Database\Factories;

use App\Enums\EnrollmentStatus;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(EnrollmentStatus::cases());
        $enrolled_at = fake()->date();

        [$enrolled_at, $withdrawn_at] = match ($status) {
            EnrollmentStatus::PROCESSING => [null, null],
            EnrollmentStatus::ENROLLED => [$enrolled_at, null],
            EnrollmentStatus::COMPLETED, EnrollmentStatus::FAILED, EnrollmentStatus::WITHDRAWN => [$enrolled_at, fake()->dateTimeBetween($enrolled_at)->format('Y-m-d')],
        };

        return[
            'student_id' => $this->faker->randomElement(Student::pluck('id')),
            'course_offering_id' => $this->faker->randomElement(CourseOffering::pluck('id')),
            'status' => $status,
            'enrolled_at' => $enrolled_at,
            'withdrawn_at' => $withdrawn_at,
        ];
    }
}
