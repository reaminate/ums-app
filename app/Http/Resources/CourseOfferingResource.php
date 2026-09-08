<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseOfferingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'course_id' => $this->course_id,
            'course_more_info' => CourseResource::make($this->whenLoaded('course')),
            'semester_id' => $this->semester_id,
            'semester_more_info' => AcademicSemesterResource::make($this->whenLoaded('semester')),
            'lecturer_id' => LecturerResource::make($this->whenLoaded('lecturer')),
            'max_students' => $this->max_students,
            'status'=> $this->status,
            'start_date'=>$this->start_date,
            'end_date'=>$this->end_date,
            'students' => $this->when(
                $request->user()?->isAdmin(),
                fn () => StudentResource::collection($this->whenLoaded('students'))
            ),
            'class_schedules' => ClassScheduleResource::collection($this->whenLoaded('classSchedules')),
            'assignments' => $this->when(
                $request->user()?->isAdmin(),
                fn () => AssignmentResource::collection($this->whenLoaded('assignments')),
            ),
            'exams' => $this->when(
                $request->user()?->isAdmin(),
                fn()=>ExamResource::collection($this->whenLoaded('exams')),
            ),
            'grades' => $this->when(
                $request->user()?->isAdmin(),
                fn()=>GradeResource::collection($this->whenLoaded('grades')),
            ),
        ];
    }
}
