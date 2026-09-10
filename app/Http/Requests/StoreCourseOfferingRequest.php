<?php

namespace App\Http\Requests;

use App\Enums\CourseOfferingStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCourseOfferingRequest extends FormRequest
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
            'course_id'=> ['required', 'integer', 'exists:courses,id'],
            'semester_id' => ['required', 'integer', 'exists:academic_semesters,id'],
            'lecturer_id' => ['required', 'integer', 'exists:lecturers,id'],
            'max_students' => ['required', 'integer', 'min:20', 'max:50'],
            'status' => ['required', new Enum(CourseOfferingStatus::class)],
            'start_date' => ['date', 'required', 'date_format:Y-m-d'],
            'end_date' => ['date', 'required', 'date_format:Y-m-d', 'after:start_date'],
        ];
    }
}
