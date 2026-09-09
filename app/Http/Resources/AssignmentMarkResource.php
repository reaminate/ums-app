<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentMarkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'assignment' => $this->when(
                $this->resource->relationLoaded('assignmentSubmission'),
                fn () => $request->user()?->isAdmin()
                    ? AssignmentSubmissionResource::make($this->assignmentSubmission)
                    : $this->assignmentSubmission->assignment->title
            ),
            'marks' => $this->marks,
            'comments' => $this->comments,
            'marked_at' => $this->marked_at,
            'lecturer_info' => $this->when(
                $request->user()?->isAdmin(),
                fn() => LecturerResource::make($this->whenLoaded('lecturer'))
            ),
        ];
    }
}
