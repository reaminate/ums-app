<?php

namespace Database\Factories;

use App\Enums\LecturerStatus;
use App\Enums\UserType;
use App\Models\Department;
use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lecturer>
 */
class LecturerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::find($this->faker->unique()->randomElement(User::where('type', UserType::LECTURER)->pluck('id')));

        return [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'department_id' => $this->faker->randomElement(Department::pluck('id')),
            'status' => $this->faker->randomElement(LecturerStatus::cases()),
        ];
    }
}
