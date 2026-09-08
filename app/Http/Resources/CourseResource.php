<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'code' => $this->code,
            'description' => $this->description,
            'department_id' => DepartmentResource::make($this->whenLoaded('department')),
            'credit_value' => $this->credit_value,
            'course_level' => $this->course_level,
            'status' => $this->status,
            'academic_programs' => AcademicProgramResource::collection($this->whenLoaded('academicPrograms')),
            'course_offerings' => CourseOfferingResource::collection($this->whenLoaded('courseOfferings')),
            'pre_requisites' => CourseResource::collection($this->whenLoaded('prerequisites')),
            'pre_requisite_for' => CourseResource::collection($this->whenLoaded('prerequisiteFor')),
        ];
    }
}
