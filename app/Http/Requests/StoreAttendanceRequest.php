<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
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
            'student_id' => ['required', 'exists:students,id'],
            'class_schedule_id' => ['required', 'exists:class_schedules,id', Rule::unique('attendances', 'class_student_id')->where('student_id', $this->input('student_id'))],
            'total_classes' => ['required', 'integer', 'max:1'],
            'attendance_value' => ['required', 'integer', 'max:1'],
            'recorded_at' => ['required', 'date_format:h:m']
        ];
    }
}
