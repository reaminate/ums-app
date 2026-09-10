<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreGradeRequest extends FormRequest
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
            'student_id' => ['required', 'exists:students,id', 'integer'],
            'course_offering_id' => ['required', 'exists:course_offerings,id'],
            'total_assignment_score' => ['required', 'decimal:2'],
            'total_test_marks' => ['required', 'decimal:2'],
        ];
    }
}
