<?php

namespace Database\Factories;

use App\Enums\SemesterStatus;
use App\Models\AcademicSemester;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicSemester>
 */
class AcademicSemesterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $semester_names = ['semester_1', 'semester_2', 'semester_3'];

        $start_date = Carbon::instance(fake()->dateTimeBetween('-2 years', '+2 years'));

        return [
            'name' => fake()->randomElement($semester_names),
            'year' => $start_date->year,
            'start_date' => $start_date->toDateString(),
            'end_date' => $start_date->copy()->addMonths(3)->toDateString(),
            'registration_start_date' => $start_date->copy()->subMonths(2)->toDateString(),
            'registration_end_date' => $start_date->copy()->subMonth()->toDateString(),
            'status' => fake()->randomElement(SemesterStatus::cases()),
        ];
    }
}
