<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignmentSubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enrollment = Enrollment::all();
        $assignments = Assignment::all();
        foreach($assignments as $assignment){
            $students_that_should = $enrollment->where('course_offering_id', $assignment->course_offering_id)->pluck('student_id');
            foreach($students_that_should as $student){
                AssignmentSubmission::factory()->create([
                    'assignment_id' => $assignment->id,
                    'student_id' => $student,
                ]);
            }
        }
    }
}
