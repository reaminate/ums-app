<?php

namespace Database\Seeders;

use App\Enums\EnrollmentStatus;
use App\Models\CourseOffering;
use App\Models\Enrollment;
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
            foreach($enrollees as $enroll){
                Enrollment::factory()->create([
                    'student_id' => $enroll->id,
                    'course_offering_id' => $courseOffering->id,
                ]);
            }
        });
    }
}
