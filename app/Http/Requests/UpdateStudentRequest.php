<?php

namespace App\Http\Requests;
use App\Enums\StudentStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
class UpdateStudentRequest extends FormRequest
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
            'user_id' => ['sometimes', 'exists:users,id', 'integer', Rule::unique('students', 'user_id')->ignore($this->route('student'))],
            'name' => ['sometimes', 'string'],
            'email' => ['sometimes', 'email'],
            'program_id' => ['sometimes', 'exists:academic_programs,id'],
            'enrollment_year' => ['sometimes', 'integer', 'digits:4'],
            'status' => ['sometimes', new Enum(StudentStatus::class)],
        ];
    }
}
