<?php

namespace App\Listeners;

use App\Events\GradeUpdate;
use App\Models\Grade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class UpdateGradesDependingOnType
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
    public function handle(GradeUpdate $event): void
    {
        $assignment_marks = $event->assginment_mark??0.0;
        $exam_mark = $event->exam_mark??0.0;
        $student_id = $exam_mark->student_id??$assignment_marks->assignment_submission_id->student_id;
        $course_offering_id = $exam_mark->exam->course_offering_id ?? $assignment_marks->assignmentSubmission->assignment->course_offering_id;
        try {
            DB::transaction(function() use($assignment_marks, $exam_mark, $student_id, $course_offering_id){
            $grade = Grade::where('student_id', $student_id)
            ->where('course_offering_id', $course_offering_id)->first()
            ->incrementEach([
                'total_assignment_score' => $assignment_marks, 
                'total_test_marks' => $exam_mark,
                ]);
            if(($grade->total_assignment_score + $grade->total_test_marks)>100.0){
                
            }    
            }); 
        } catch (\Throwable $th) {
            //throw $th;
        }
        
         
    }
}
