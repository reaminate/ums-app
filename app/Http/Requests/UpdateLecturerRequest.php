<?php

namespace App\Http\Requests;
use App\Enums\LecturerStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
class UpdateLecturerRequest extends FormRequest
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
            'user_id' => ['integer', 'exists:users,id', 'sometimes'],
            'name' => ['string', 'sometimes'],
            'email' => ['email', 'sometimes'],
            'department_id' => ['sometimes', 'exists:departments,id', 'integer'],
            'status' => ['sometimes', new Enum(LecturerStatus::cases())],
        ];
    }
}
