<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentSubmissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'assignment_name' => $this->whenLoaded('assignment', fn() => $this->assignment->title),
            'student_info' => StudentResource::make($this->whenLoaded('student')),
            'file_name' => $this->original_name,
            'file_more_info' => $this->when(
                $request->user()?->isAdmin(),
                fn() => 
                [
                    'file_path' => $this->file_path,
                    'mime_type' => $this->mime_type,
                ]
            ),
            'comments' => $this->comments,
            'submitted_at' => $this->submitted_at,
            'status' => $this->status,
            'assignment_more_info' => $this->when(
                $request->user()?->isAdmin(),
                fn() => 
                [
                    'assignment_mark' => AssignmentMarkResource::make($this->whenLoaded('assignmentMark')),
                    'assignment' => AssignmentResource::make($this->whenLoaded('assignment')),
                ]
            ),
        ];
    }
}
