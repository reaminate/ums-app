<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
            'class' => $this->whenLoaded(
                'classSchedule',
                fn () => $this->classSchedule->courseOffering->course->name,
            ),
            'attendance_value' => $this->attendance_value,
            'status' => $this->status,
            'recorded_at' => $this->recorded_at
        ];
    }
}
