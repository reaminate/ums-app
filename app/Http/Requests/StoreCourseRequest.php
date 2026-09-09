<?php

namespace App\Http\Requests;

use App\Enums\CourseStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCourseRequest extends FormRequest
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
            'name' => ['required', 'string', 'unique:courses,name'],
            'description' => ['string'],
            'department_id' => ['required', 'exists:departments,id', 'integer'],
            'credit_value' => ['required', 'integer', 'min_digits:3', 'max_digits:4','min:100'],
            'course_level' =>['required', 'integer', 'max_digits:1', 'min_digits:0', 'min:1'],
            'status' => ['required', new Enum(CourseStatus::cases())],
        ];
    }
}
