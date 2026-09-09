<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademicSemesterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'year' => $this->year,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'registration_start_date' => $this->registration_start_date,
            'registration_end_date' => $this->registration_end_date,
            'status' => $this->status,
            'course_offerings' => $this->when(
                $this->resource->relationLoaded('courseOfferings'),
                fn () => $request->user()?->isAdmin()
                    ? CourseOfferingResource::collection($this->courseOfferings)
                    : $this->courseOfferings->pluck('course.name')
            ),
        ];
    }
}
