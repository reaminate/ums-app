<?php

namespace App\Listeners;

use App\Events\CreateGrade;
use App\Models\Grade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateGradeRowForStudentAndCourseOffering
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CreateGrade $event): void
    {
        $student = $event->student;
        $course_offering = $event->course_offering;

        Grade::create([
            'student_id' => $student->id,
            'course_offering_id' => $course_offering->id,
            'total_assignment_score' => 0.0,
            'total_test_marks' => 0.0,
        ]);
    }
}
