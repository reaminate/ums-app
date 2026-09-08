<?php

namespace Database\Factories;

use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends Factory<AssignmentSubmission>
 */
class AssignmentSubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $assignment = Assignment::inRandomOrder()->firstOrFail();
        $dueDate = Carbon::parse($assignment->due_date);

        $students_belonging = $assignment->courseOffering->students()->pluck('students.id')->all();
        $mimeType = $this->faker->randomElement(['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
        $extension = $mimeType === 'application/pdf' ? 'pdf' : 'docx';
        $originalName = $this->faker->slug(3).'.'.$extension;

        $path = UploadedFile::fake()
            ->create($originalName, $this->faker->numberBetween(50, 800))
            ->store('assignment-submissions', 'local');

        $submittedAt = $this->faker->dateTimeBetween($dueDate->copy()->subWeek(), $dueDate->copy()->addDays(3));
        
        
        return [
            'assignment_id' => $assignment->id,
            'student_id' => $this->faker->randomElement($students_belonging),
            'file_path' => $path,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'comments' => $this->faker->optional()->sentence(),
            'submitted_at' => $submittedAt,
            'status' => $submittedAt > $dueDate ? SubmissionStatus::LATE : SubmissionStatus::ONTIME,
        ];
    }
}
