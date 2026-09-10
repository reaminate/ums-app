<?php

namespace App\Http\Requests;
use App\Enums\DaysOfTheWeek;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
class UpdateClassScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_offering_id' => ['integer', 'sometimes', 'exists:course_offerings,id'],
            'day' => ['sometimes', new Enum(DaysOfTheWeek::cases())],
            'start_time' => ['sometimes', 'date_format:h:i'],
            'end_time' => ['sometimes', 'date_format:h:i', 'after:start_time'],
            'room_number' => ['sometimes', 'string']
        ];
    }
}
