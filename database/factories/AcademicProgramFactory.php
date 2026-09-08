<?php

namespace Database\Factories;

use App\Enums\AcademicStatus;
use App\Models\AcademicProgram;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicProgram>
 */
class AcademicProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //for the name
        $starting = ['Bachelor in', 'Diploma in', 'Masters in', 'Doctorate in'];
        $starting_code = $this->faker->randomElement($starting);
        $actual_name = fake()->unique()->words(2, true);
        $name = "$starting_code $actual_name";
        return [
            'name' => $name,
            'department_id' => $this->faker->randomElement(Department::pluck('id')),
            'qualification_level' => fake()->numberBetween(5, 9),
            'duration' => fake()->numberBetween(0,5),
            'required_credits' => fake()->numberBetween(2,9)*1000,
            'status' => $this->faker->randomElement(AcademicStatus::cases()),
        ];
    }
}
