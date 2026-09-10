<?php

namespace App\Http\Requests;

use App\Enums\DaysOfTheWeek;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreClassScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_offering_id' => ['integer', 'required', 'exists:course_offerings,id'],
            'day' => ['required', new Enum(DaysOfTheWeek::cases())],
            'start_time' => ['required', 'date_format:h:i'],
            'end_time' => ['required', 'date_format:h:i', 'after:start_time'],
            'room_number' => ['required', 'string']
        ];
    }
}
