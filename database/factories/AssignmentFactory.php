<?php

namespace Database\Factories;

use App\Enums\AssignmentStatus;
use App\Models\Assignment;
use App\Models\CourseOffering;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends Factory<Assignment>
 */
class AssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mimeType = $this->faker->randomElement(['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
        $extension = $mimeType === 'application/pdf' ? 'pdf' : 'docx';
        $originalName = $this->faker->slug(3).'.'.$extension;

        $path = UploadedFile::fake()
            ->create($originalName, $this->faker->numberBetween(50, 800))
            ->store('assignments', 'local');

        return [
            'course_offering_id' => $this->faker->randomElement(CourseOffering::pluck('id')),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraphs(3, true),
            'due_date' => $this->faker->dateTimeBetween('now', '+2 months'),
            'max_marks' => $this->faker->randomFloat(1, 20, 100),
            'file_path' => $path,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'status' => $this->faker->randomElement(AssignmentStatus::cases()),
        ];
    }
}
