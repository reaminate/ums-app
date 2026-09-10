<?php

namespace App\Http\Requests;
use App\Enums\StudentStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
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
            'user_id' => ['sometimes', 'exists:users,id', 'integer', 'unique:users,id'],
            'student_number' => ['sometimes', 'string', 'starts_with:S0', 'unique:students,student_number'],
            'name' => ['sometimes', 'string'],
            'email' => ['sometimes', 'email'],
            'program_id' => ['sometimes', 'exists:academic_programs,id'],
            'enrollment_year' => ['sometimes', 'integer', 'digits:4'],
            'status' => ['sometimes', new Enum(StudentStatus::cases())],
        ];
    }
}
