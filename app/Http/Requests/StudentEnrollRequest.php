<?php

namespace App\Http\Requests;

use App\Enums\EnrollmentStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StudentEnrollRequest extends FormRequest
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
            'course_offering_id' => ['required', 'array', 'max:4'],
            'course_offering_id.*' => [
                'integer',
                'distinct',
                Rule::exists('enrollment', 'course_offering_id')->where('student_id', $this->route('student')?->id),
            ],
            'status' => ['required', new Enum(EnrollmentStatus::class)],
            'enrolled_at' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'withdrawn_at' => ['sometimes', 'date', 'date_format:Y-m-d']
        ];
    }
}
