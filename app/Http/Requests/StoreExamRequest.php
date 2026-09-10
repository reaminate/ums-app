<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
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
            'course_offering_id' => ['required', 'exists:course_offerings,id', 'integer'],
            'exam_type' => ['required', 'string'],
            'exam_date' => ['required', 'date', 'date_format:Y-m-d'],
            'max_marks' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'weight' => ['required', 'integer', 'between:0,100'],
        ];
    }
}
