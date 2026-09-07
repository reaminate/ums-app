<?php

namespace Database\Factories;

use App\Enums\DaysOfTheWeek;
use App\Models\ClassSchedule;
use App\Models\CourseOffering;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassSchedule>
 */
class ClassScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        
        return [
            'course_offering_id' => $this->faker->randomElement(CourseOffering::pluck('id')),
            'day' => $this->faker->randomElement(DaysOfTheWeek::cases()),
            'start_time' => fake()->time(),
            'end_time' => fake()->time(),
            'room_number' => fake()->words(7),
        ];
    }
}
