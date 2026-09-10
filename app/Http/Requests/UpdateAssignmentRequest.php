<?php

namespace App\Http\Requests;
use App\Enums\AssignmentStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
class UpdateAssignmentRequest extends FormRequest
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
            'course_offering_id' => ['sometimes', 'exists:course_offerings,id'],
            'title' => ['sometimes', 'string'],
            'description' => ['nullable', 'string'],
            'due_date' => ['sometimes', 'date_format:Y-m-d', 'date'],
            'max_marks' => ['sometimes', 'numeric', 'decimal:0,2'],
            'file' => ['sometimes', 'file', 'mimes:pdf,docx', 'max:5120'],
            'status' => ['sometimes', new Enum(AssignmentStatus::class)],
        ];
    }
}
