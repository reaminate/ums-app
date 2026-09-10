<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExamMarkRequest extends FormRequest
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
            'exam_id' => ['sometimes', 'exists:exams,id', 'integer', Rule::unique('exam_marks', 'exam_id')->where('student_id', $this->input('student_id'))->ignore($this->route('exam_mark'))],
            'student_id' => ['sometimes', 'exists:students,id', 'integer'],
            'marks' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
        ];
    }
}
