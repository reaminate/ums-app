<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'student_id' => $this->student_id,
            'student_info' => $this->when(
                $request->user()?->isAdmin(),
                fn()=>StudentResource::make($this->whenLoaded('student'))
            ),
            'course_offering_name' => $this->whenLoaded(
                'courseOffering',
                fn () => $this->courseOffering->course->name,
            ),
            'total_assignment_score' => $this->total_assignment_score,
            'total_test_marks' => $this->total_test_marks,
            'grade_score' => $this->grade_score
        ];
    }
}
