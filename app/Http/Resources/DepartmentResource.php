<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
            'faculty_id' => FacultyResource::make($this->whenLoaded('faculty')),
            'academic_programs' => AcademicProgramResource::collection($this->whenLoaded('academicPrograms')),
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            'lecturers' => LecturerResource::collection($this->whenLoaded('lecturers')),
        ];
    }
}
