<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamMarkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'exam' => $this->when(
                $this->resource->relationLoaded('exam'),
                fn () => $request->user()?->isAdmin()
                    ? ExamResource::make($this->exam)
                    : [$this->exam->courseOffering->course->name, $this->exam->exam_type]
            ),
            'student' => $this->when(
                $this->resource->relationLoaded('student'),
                fn () => $request->user()?->isAdmin()
                    ? StudentResource::make($this->student)
                    : $this->student->name
            ),
            'marks' => $this->marks,
            
        ];
    }
}
