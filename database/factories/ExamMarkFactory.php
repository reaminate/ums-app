<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\ExamMark;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamMark>
 */
class ExamMarkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $exam = Exam::whereHas('courseOffering.students')->inRandomOrder()->firstOrFail();
        $student = $exam->courseOffering->students()->inRandomOrder()->firstOrFail();

        return [
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'marks' => fake()->numberBetween(0, $exam->max_marks),
        ];
    }
}
