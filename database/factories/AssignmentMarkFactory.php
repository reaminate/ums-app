<?php

namespace Database\Factories;

use App\Models\AssignmentMark;
use App\Models\AssignmentSubmission;
use App\Models\Lecturer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssignmentMark>
 */
class AssignmentMarkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $assignment_submission = AssignmentSubmission::inRandomOrder()->firstOrFail();
        $submittedAt = Carbon::parse($assignment_submission->submitted_at);

        return [
            'assignment_submission_id' => $assignment_submission->id,
            'marks' => fake()->numberBetween(0, $assignment_submission->assignment->max_marks),
            'comments' => fake()->sentences(2, true),
            'marked_at' => fake()->dateTimeBetween($submittedAt, $submittedAt->isFuture() ? $submittedAt->copy()->addWeek() : 'now'),
            'lecturer_id' => $this->faker->randomElement(Lecturer::pluck('id')),
        ];
    }
}
