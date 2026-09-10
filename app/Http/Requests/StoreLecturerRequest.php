<?php

namespace App\Http\Requests;

use App\Enums\LecturerStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreLecturerRequest extends FormRequest
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
            'user_id' => ['integer', 'exists:users,id', 'required'],
            'name' => ['string', 'required'],
            'email' => ['email', 'required'],
            'department_id' => ['required', 'exists:departments,id', 'integer'],
            'status' => ['required', new Enum(LecturerStatus::cases())],
        ];
    }
}
