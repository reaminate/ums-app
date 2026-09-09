<?php

namespace App\Http\Resources;

use App\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'student_number' => $this->when(
                $this->type == UserType::STUDENT->value,
                fn () => $request->user()?->isAdmin()
                    ? StudentResource::make($this->whenLoaded('student'))
                    : $this->student->student_number
            ),
            'staff_number' => $this->when(
                $this->type == UserType::LECTURER->value,
                fn () => $request->user()?->isAdmin()
                    ? LecturerResource::make($this->whenLoaded('lecturer'))
                    : $this->lecturer->staff_number
            ),
            'type' => $this->when(
                $request->user()?->isAdmin(),
                $this->type
            ),
        ];
    }
}
