<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\CourseOffering;
use App\Notifications\NewAssignmentPublished;

class AssignmentService
{
    /**
     * storing an assignment
     */
    public function store(array $validated): Assignment 
    {
        $file = $validated['file'];
        $stored_path = $file->store('assignments', 'public');

        $assignment = Assignment::create([
            'course_offering_id' => $validated['course_offering_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_date' => $validated['due_date'],
            'max_marks' => $validated['max_marks'],
            'file_path' => $stored_path,
            'original_name' =>  $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'status' => $validated['status'],
        ]);
        $course_offering = CourseOffering::findOrFail($assignment->course_offering_id);
        $students = $course_offering->students;
        foreach($students as $student){
            $student->user->notify(new NewAssignmentPublished($assignment, $student));
        }
        return $assignment;
    }
    public function update(array $validated, Assignment $assignment): Assignment 
    {
        if(isset($validated['file'])){
            $file = $validated['file'];
            $stored_path = $file->store('assignments', 'public');
            $validated['file_path'] = $stored_path;
            $validated['original_name'] = $file->getClientOriginalName();
            $validated['mime_type'] = $file->getMimeType();
            unset($validated['file']);
        }
        $assignment->update($validated);
        return $assignment;
    }
    
    
}
