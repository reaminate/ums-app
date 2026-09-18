<?php

namespace App\Services;

use App\Events\GradeUpdate;
use App\Models\AssignmentMark;
use App\Models\AssignmentSubmission;
use App\Notifications\AssignmentGraded;

class AssignmentMarkService
{
    /**
     * Create an assignment mark, weighting it by the submission's on-time/late status.
     */
    public function store(array $validated, int $lecturerId): AssignmentMark
    {
        $assignment_submission = AssignmentSubmission::findOrFail($validated['assignment_submission_id']);

        $validated['marks'] *= $assignment_submission->status->weight();
        $validated['marked_at'] = now();
        $validated['lecturer_id'] = $lecturerId;

        $assignment_mark = AssignmentMark::create($validated);

        $student = $assignment_mark->assignmentSubmission->student;
        $student->user->notify(new AssignmentGraded($assignment_mark));

        return $assignment_mark;
    }

    /**
     * Update an assignment mark, dispatching a grade update when the lecturer confirms it.
     */
    public function update(AssignmentMark $assignment_mark, array $validated, int $lecturerId): AssignmentMark
    {
        $assignment_submission = AssignmentSubmission::findOrFail($validated['assignment_submission_id']);
        if (isset($validated['marks'])) {
            $validated['marks'] *= $assignment_submission->status->weight();
        }
        $validated['marked_at'] = now();
        $validated['lecturer_id'] = $lecturerId;
        $confirm = $validated['confirm'] ?? false;
        unset($validated['confirm']);

        $assignment_mark->update($validated);

        if ($confirm) {
            GradeUpdate::dispatch($assignment_mark);
        }

        return $assignment_mark;
    }
}
