<?php

namespace App\Listeners;

use App\Events\GradeUpdate;
use App\Models\Grade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

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
        $assignmentMark = $event->assginment_mark;
        $examMark = $event->exam_mark;

        $studentId = $examMark->student_id ?? $assignmentMark->assignmentSubmission->student_id;
        $courseOfferingId = $examMark->exam->course_offering_id ?? $assignmentMark->assignmentSubmission->assignment->course_offering_id;
        $assignmentScore = $assignmentMark->marks ?? 0.0;
        $testMarks = $examMark->marks ?? 0.0;

        try {
            DB::transaction(function () use ($assignmentScore, $testMarks, $studentId, $courseOfferingId) {
                $grade = Grade::where('student_id', $studentId)
                    ->where('course_offering_id', $courseOfferingId)
                    ->first();

                if (! $grade) {
                    throw new InvalidArgumentException("No grade found for student {$studentId} in course offering {$courseOfferingId}.");
                }

                $grade->incrementEach([
                    'total_assignment_score' => $assignmentScore,
                    'total_test_marks' => $testMarks,
                ]);

                throw_if(($grade->total_assignment_score + $grade->total_test_marks) > 100.0, InvalidArgumentException::class);
            });
        } catch (InvalidArgumentException $e) {
            report($e);
        }
    }
}
