<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'course_name' => $this->whenLoaded(
                'courseOffering',
                fn () => $this->courseOffering->course->name,
            ),
            'course_more_info' => CourseOfferingResource::make($this->whenLoaded('courseOffering')),
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date,
            'max_marks' => $this->max_marks,
            'file_name' => $this->original_name,
            'status' => $this->status,
            'assignment_submissions' => $this->when(
                $request->user()?->isAdmin(),
                fn()=>AssignmentSubmissionResource::collection($this->whenLoaded('assignmentSubmissions'))
            ),
        ];
    }
}
