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
        $attendance_value = fake()->numberBetween(0,100);
        $status = AttendanceStatus::NOTVIABLE->value;
        switch(true){
            case ($attendance_value>75): $status = AttendanceStatus::VIABLE->value;
            break;
            case ($attendance_value>70): $status = AttendanceStatus::BARELY_VIABLE->value;
            break;
            default : $status = AttendanceStatus::NOTVIABLE->value;
            break;
        }
        return [
            'class_schedule_id' => fn () => ClassSchedule::whereHas('courseOffering.students')->inRandomOrder()->firstOrFail()->id,
            'student_id' => fn (array $attributes) => ClassSchedule::findOrFail($attributes['class_schedule_id'])
                ->courseOffering->students()->inRandomOrder()->firstOrFail()->id,
            'total_classes' => fake()->numberBetween(20, 50),
            'attendance_value' => $attendance_value,
            'status' => $status,
            'recorded_at' => function (array $attributes) {
                $classSchedule = ClassSchedule::findOrFail($attributes['class_schedule_id']);

                return fake()->dateTimeBetween($classSchedule->start_time, $classSchedule->end_time);
            },
        ];
    }
}
