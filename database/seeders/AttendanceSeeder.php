<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ClassSchedule;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClassSchedule::with('courseOffering.students')->get()->each(function (ClassSchedule $classSchedule) {
            $classSchedule->courseOffering->students->each(function ($student) use ($classSchedule) {
                Attendance::factory()->create([
                    'student_id' => $student->id,
                    'class_schedule_id' => $classSchedule->id,
                    'recorded_at' => fake()->dateTimeBetween($classSchedule->start_time, $classSchedule->end_time),
                ]);
            });
        });
    }
}
