<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'course' => $this->when(
                $this->resource->relationLoaded('courseOffering'),
                fn() => $request->user()?->isAdmin()
                ? CourseOfferingResource::make($this->courseOffering)
                : $this->courseOffering->course->code,
            ),
            'exam_type' => $this->exam_type,
            'exam_date' => $this->exam_date,
            'max_marks' => $this->max_marks,
            'weight' => $this->weight,
            'exam_marks' => $this->when(
                $request->user()?->isAdmin(),
                fn() => ExamMarkResource::collection($this->whenLoaded('examMarks')),
            ),
        ];
    }
}
