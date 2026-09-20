<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;

class AssignmentSubmissionService
{
    /**
     * for creating a submission
     */
    public function create(array $validated, Student $student): AssignmentSubmission
    {
        $file = $validated['file'];
        $stored_path = $file->store('assignments', 'public');
        $assignment = Assignment::findOrFail($validated['assignment_id']);
        $assignment_submission = AssignmentSubmission::create([
            'assignment_id' => $validated['assignment_id'],
            'student_id' => $student->id,
            'file_path' => $stored_path,
            'original_name' =>  $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'comments' => $validated['comments'],
            'submitted_at' => now(),
            'status' => (now() < $assignment->due_date) ? SubmissionStatus::ONTIME: SubmissionStatus::LATE,
        ]);
        return $assignment_submission;
    }
    
    /**
     * for updating a submission
     */
    public function update(array $validated, AssignmentSubmission $assignment_submission, Student $student): AssignmentSubmission
    {
        $assignment = Assignment::findOrFail($validated['assignment_id'] ?? $assignment_submission->assignment_id);
        $validated['student_id'] = $student->id;
        $validated['submitted_at'] = now();
        $validated['status'] = (now() < $assignment->due_date) ? SubmissionStatus::ONTIME: SubmissionStatus::LATE;
        if(isset($validated['file'])){
            $file = $validated['file'];
            $stored_path = $file->store('assignments', 'public');
            $validated['file_path'] = $stored_path;
            $validated['original_name'] = $file->getClientOriginalName();
            $validated['mime_type'] = $file->getMimeType();
            unset($validated['file']);
        }
        $assignment_submission->update($validated);
        return $assignment_submission;
    }
}
