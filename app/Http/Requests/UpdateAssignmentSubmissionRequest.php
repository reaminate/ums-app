<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateAssignmentSubmissionRequest extends FormRequest
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
            'assignment_id' => ['sometimes', 'exists:assignments,id', 'integer', Rule::unique('assignment_submissions', 'assignment_id')->where('student_id', $this->input('student_id'))->ignore($this->route('assignment_submission'))],
            'student_id' => ['sometimes', 'exists:students,id', 'integer'],
            'file' => ['sometimes', 'file', 'mimes:pdf,docx', 'max:5120'],
            'comments' => ['sometimes', 'string', 'max:200'],
            'submited_at' => ['date', 'sometimes', 'date_format:Y-m-d'],
        ];
    }
}
