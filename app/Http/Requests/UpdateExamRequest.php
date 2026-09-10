<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExamRequest extends FormRequest
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
            'course_offering_id' => ['sometimes', 'exists:course_offerings,id', 'integer'],
            'exam_type' => ['sometimes', 'string'],
            'exam_date' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'max_marks' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'weight' => ['sometimes', 'integer', 'between:0,100'],
        ];
    }
}
