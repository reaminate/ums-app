<?php

namespace Database\Seeders;

use App\Models\AssignmentMark;
use App\Models\Enrollment;
use App\Models\ExamMark;
use App\Models\Grade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Enrollment::all(['student_id', 'course_offering_id'])->each(function (Enrollment $enrollment) {
            $totalAssignmentScore = AssignmentMark::whereHas('assignmentSubmission', function ($query) use ($enrollment) {
                $query->where('student_id', $enrollment->student_id)
                    ->whereHas('assignment', fn ($q) => $q->where('course_offering_id', $enrollment->course_offering_id));
            })->sum('marks');

            $totalTestMarks = ExamMark::where('student_id', $enrollment->student_id)
                ->whereHas('exam', fn ($q) => $q->where('course_offering_id', $enrollment->course_offering_id))
                ->sum('marks');

            Grade::factory()->create([
                'student_id' => $enrollment->student_id,
                'course_offering_id' => $enrollment->course_offering_id,
                'total_assignment_score' => $totalAssignmentScore,
                'total_test_marks' => $totalTestMarks,
            ]);
        });
    }
}
