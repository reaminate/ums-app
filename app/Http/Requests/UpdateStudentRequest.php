<?php

namespace App\Http\Requests;
use App\Enums\CourseOfferingStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\StudentStatus;
use App\Models\CourseOffering;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
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
            'enrollment_status' => ['sometimes', new Enum(EnrollmentStatus::class)],
            'course_offerings' => ['sometimes', 'array', 'max:4'],
            'course_offerings.*' => [
                'integer',
                'distinct',
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
