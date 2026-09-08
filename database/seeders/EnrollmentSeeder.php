<?php

namespace Database\Seeders;

use App\Enums\EnrollmentStatus;
use App\Models\CourseOffering;
use App\Models\Student;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();

        if ($students->isEmpty()) {
            return;
        }

        CourseOffering::all()->each(function (CourseOffering $courseOffering) use ($students) {
            $enrollees = $students->random(min($courseOffering->max_students, $students->count()));

            $courseOffering->students()->attach(
                $enrollees->mapWithKeys(fn (Student $student) => [
                    $student->id => [
                        'status' => EnrollmentStatus::ENROLLED->value,
                        'enrolled_at' => $courseOffering->start_date,
                    ],
                ])
            );
        });
    }
}
