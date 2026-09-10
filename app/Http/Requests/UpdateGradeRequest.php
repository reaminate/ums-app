<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGradeRequest extends FormRequest
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
            'student_id' => ['sometimes', 'exists:students,id', 'integer'],
            'course_offering_id' => ['sometimes', 'exists:course_offerings,id'],
            'total_assignment_score' => ['sometimes', 'decimal:2'],
            'total_test_marks' => ['sometimes', 'decimal:2'],
        ];
    }
}
