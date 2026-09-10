<?php

namespace App\Http\Requests;

use App\Enums\SemesterStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreAcademicSemesterRequest extends FormRequest
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
            'name' => ['required','string', 'starts_with:Semester'],
            'year' => ['required','integer', 'digits:4'],
            'start_date'=>['required','date', 'date_format:Y-m-d'],
            'end_date' => ['required','date', 'date_format:Y-m-d', 'after:start_date'],
            'registration_start_date' => ['required', 'date', 'date_format:Y-m-d', 'before:start_date'],
            'registration_end_date' => ['required', 'date', 'date_format:Y-m-d', 'after:registration_start_date', 'before:start_date'],
            'status' => ['required', new Enum(SemesterStatus::class)]
        ];
    }
}
