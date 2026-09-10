<?php

namespace App\Http\Requests;
use App\Enums\AcademicStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
class UpdateAcademicProgramRequest extends FormRequest
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
            'name' => ['sometimes', 'string', Rule::unique('academic_programs', 'name')->ignore($this->route('academic_program')), 'starts_with:diploma,degree,masters,phd,doctorate'],
            'department_id' => ['sometimes', 'exists:departments,id', 'integer', 'max_digits:2'],
            'qualification_level' => ['sometimes', 'integer', 'max_digits:2', 'min:0'],
            'duration' => ['sometimes', 'integer', 'min:1', 'max_digits:1'],
            'required_credits' => ['sometimes', 'integer', 'min:1000', 'min_digits:4', 'max_digits:6'],
            'status' => ['sometimes', new Enum(AcademicStatus::class)]
            
        ];
    }
}
