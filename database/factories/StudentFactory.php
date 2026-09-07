<?php

namespace Database\Factories;

use App\Enums\StudentStatus;
use App\Enums\UserType;
use App\Models\AcademicProgram;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::find($this->faker->unique()->randomElement(User::where('type', UserType::STUDENT)->pluck('id')));
        return [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'program_id' => $this->faker->randomElement(AcademicProgram::pluck('id')),
            'enrollment_year' => fake()->year(),
            'status' => $this->faker->randomElement(StudentStatus::cases()),
        ];
    }
}
