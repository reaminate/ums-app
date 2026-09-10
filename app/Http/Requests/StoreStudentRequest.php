<?php

namespace App\Http\Requests;

use App\Enums\StudentStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreStudentRequest extends FormRequest
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
            'user_id' => ['required', 'exists:users,id', 'integer', 'unique:users,id'],
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'program_id' => ['required', 'exists:academic_programs,id'],
            'enrollment_year' => ['required', 'integer', 'digits:4'],
            'status' => ['required', new Enum(StudentStatus::cases())],
        ];
    }
}
