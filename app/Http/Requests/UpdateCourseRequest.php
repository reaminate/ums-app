<?php

namespace App\Http\Requests;
use App\Enums\CourseStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
class UpdateCourseRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'unique:courses,name'],
            'code' => ['sometimes', 'string', 'unique:courses,code'],
            'description' => ['string'],
            'department_id' => ['sometimes', 'exists:departments,id', 'integer'],
            'credit_value' => ['sometimes', 'integer', 'min_digits:3', 'max_digits:4','min:100'],
            'course_level' =>['sometimes', 'integer', 'max_digits:1', 'min_digits:0', 'min:1'],
            'status' => ['sometimes', new Enum(CourseStatus::cases())],
        ];
    }
}
