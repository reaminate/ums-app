<?php

namespace App\Http\Requests;

use App\Enums\CourseOfferingStatus;
use App\Enums\StudentStatus;
use App\Models\CourseOffering;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
            'user_id' => ['required', 'exists:users,id', 'integer', 'unique:students,user_id'],
            'program_id' => ['required', 'exists:academic_programs,id'],
            'course_offerings' => ['sometimes', 'array','max:4'],
            'course_offerings.*' => [
                'integer','distinct',
                Rule::exists('course_offerings', 'id')->where('status', CourseOfferingStatus::OPEN->value),
            ],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $offeringIds = collect($this->input('course_offerings', []));

            if ($offeringIds->isEmpty()) {
                return;
            }

            $courseIdsByOffering = CourseOffering::whereIn('id', $offeringIds)->pluck('course_id', 'id');

            $seenCourseIds = [];

            foreach ($offeringIds as $index => $offeringId) {
                $courseId = $courseIdsByOffering[$offeringId] ?? null;

                if ($courseId === null) {
                    continue;
                }

                if (\in_array($courseId, $seenCourseIds, true)) {
                    $validator->errors()->add(
                        "course_offerings.$index",
                        'You cannot enroll in more than one offering of the same course.'
                    );
                }

                $seenCourseIds[] = $courseId;
            }
        });
    }
}
