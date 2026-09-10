<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAssignmentMarkRequest extends FormRequest
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
            'assignment_submission_id' => ['sometimes', 'exists:assignment_submissions,id', 'integer'],
            'marks' => ['sometimes', 'numeric', 'decimal:0,2'],
            'comments' => ['sometimes', 'string', 'max:200'],
            'marked_at' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'lecturer_id' => ['sometimes', 'exists:lecturers,id', 'integer'],
        ];
    }
}
