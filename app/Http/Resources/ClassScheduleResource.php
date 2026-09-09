<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'course_offering_id' => $this->course_offering_id,
            'course_offering_more_info' => CourseOfferingResource::make($this->whenLoaded('courseOffering')),
            'day' => $this->day,
            'room_number' => $this->room_number,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'attendances' => $this->when(
                $request->user()?->isAdmin(),
                fn()=>AttendanceResource::collection($this->whenLoaded('attendances')),
            ),
        ];
    }
}
