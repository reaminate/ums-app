<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LecturerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_more_info' => $this->when(
                $request->user()?->isAdmin(),
                fn() => UserResource::make($this->whenLoaded('user')),
            ),
            'staff_number' => $this->staff_number,
            'name' => $this->name,
            'email'=> $this->email,
            'department_id' =>$this->department_id,
            'department_more_info' => DepartmentResource::make($this->whenLoaded('department')),
            'status' => $this->status,
        ];
    }
}
