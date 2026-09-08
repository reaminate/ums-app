<?php

namespace Database\Factories;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\ClassSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'class_schedule_id' => fn () => ClassSchedule::whereHas('courseOffering.students')->inRandomOrder()->firstOrFail()->id,
            'student_id' => fn (array $attributes) => ClassSchedule::findOrFail($attributes['class_schedule_id'])
                ->courseOffering->students()->inRandomOrder()->firstOrFail()->id,
            'status' => $this->faker->randomElement(AttendanceStatus::cases()),
            'recorded_at' => function (array $attributes) {
                $classSchedule = ClassSchedule::findOrFail($attributes['class_schedule_id']);

                return fake()->dateTimeBetween($classSchedule->start_time, $classSchedule->end_time);
            },
        ];
    }
}
