<?php

namespace App\Http\Requests;

use App\Enums\AssignmentStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreAssignmentRequest extends FormRequest
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
            'course_offering_id' => ['required', 'exists:course_offerings,id'],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'due_date' => ['required', 'date_format:y-m-d', 'date'],
            'max_marks' => ['required', 'numeric', 'decimal:0,2'],
            'file' => ['required', 'file', 'mimes:application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'max:5120'],
            'status' => ['required', new Enum(AssignmentStatus::cases())],
        ];
    }
}
