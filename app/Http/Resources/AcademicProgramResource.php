<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademicProgramResource extends JsonResource
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
            'department_id' => $this->department_id,
            'department_more_info' => DepartmentResource::make($this->whenLoaded('department')),
            'qualification_level' => $this->qualification_level,
            'duration' => $this->duration,
            'required_credits' => $this->required_credits,
            'status' => $this->status,
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            'students' => $this->when(
                $request->user()?->isAdmin(),
                fn () => StudentResource::collection($this->whenLoaded('students'))
            ),
        ];
    }
}
