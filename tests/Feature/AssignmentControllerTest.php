<?php

namespace Tests\Feature;

use App\Enums\AssignmentStatus;
use App\Notifications\NewAssignmentPublished;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class AssignmentControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function test_a_lecturer_can_publish_an_assignment(): void
    {
        Storage::fake('public');
        Notification::fake();
        $lecturer = $this->lecturer();
        $courseOffering = $this->courseOffering($lecturer);
        $student = $this->student();
        $this->enroll($student, $courseOffering);

        Sanctum::actingAs($lecturer->user);

        $response = $this->postJson('/api/assignment', [
            'course_offering_id' => $courseOffering->id,
            'title' => 'Assignment 1',
            'description' => 'Complete the exercises',
            'due_date' => now()->addWeek()->toDateString(),
            'max_marks' => 100,
            'file' => UploadedFile::fake()->create('assignment.pdf', 100, 'application/pdf'),
            'status' => AssignmentStatus::SHOWN->value,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('assignments', ['course_offering_id' => $courseOffering->id, 'title' => 'Assignment 1']);
        Notification::assertSentTo($student->user, NewAssignmentPublished::class);
    }

    public function test_creating_an_assignment_requires_a_lecturer_or_admin(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->studentUser());
        $courseOffering = $this->courseOffering();

        $response = $this->postJson('/api/assignment', [
            'course_offering_id' => $courseOffering->id,
            'title' => 'Assignment 1',
            'description' => 'Complete the exercises',
            'due_date' => now()->addWeek()->toDateString(),
            'max_marks' => 100,
            'file' => UploadedFile::fake()->create('assignment.pdf', 100, 'application/pdf'),
            'status' => AssignmentStatus::SHOWN->value,
        ]);

        $response->assertForbidden();
    }

    public function test_store_requires_a_pdf_or_docx_file(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->lecturerUser());
        $courseOffering = $this->courseOffering();

        $response = $this->postJson('/api/assignment', [
            'course_offering_id' => $courseOffering->id,
            'title' => 'Assignment 1',
            'description' => 'Complete the exercises',
            'due_date' => now()->addWeek()->toDateString(),
            'max_marks' => 100,
            'file' => UploadedFile::fake()->create('assignment.exe', 100, 'application/octet-stream'),
            'status' => AssignmentStatus::SHOWN->value,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('file');
    }

    public function test_the_teaching_lecturer_can_update_an_assignment(): void
    {
        Storage::fake('public');
        $lecturer = $this->lecturer();
        $courseOffering = $this->courseOffering($lecturer);
        $assignment = $this->assignment($courseOffering);

        Sanctum::actingAs($lecturer->user);

        $response = $this->putJson("/api/assignment/{$assignment->id}", [
            'title' => 'Updated title',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('assignments', ['id' => $assignment->id, 'title' => 'Updated title']);
    }

    public function test_updating_an_assignment_requires_the_teaching_lecturer(): void
    {
        $assignment = $this->assignment();

        Sanctum::actingAs($this->lecturerUser());

        $response = $this->putJson("/api/assignment/{$assignment->id}", [
            'title' => 'Updated title',
        ]);

        $response->assertForbidden();
    }

    public function test_the_teaching_lecturer_can_delete_an_assignment(): void
    {
        $lecturer = $this->lecturer();
        $courseOffering = $this->courseOffering($lecturer);
        $assignment = $this->assignment($courseOffering);

        Sanctum::actingAs($lecturer->user);

        $response = $this->deleteJson("/api/assignment/{$assignment->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('assignments', ['id' => $assignment->id]);
    }

    public function test_deleting_an_assignment_requires_the_teaching_lecturer(): void
    {
        $assignment = $this->assignment();

        Sanctum::actingAs($this->lecturerUser());

        $response = $this->deleteJson("/api/assignment/{$assignment->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('assignments', ['id' => $assignment->id]);
    }

    public function test_show_only_returns_submissions_to_a_lecturer(): void
    {
        $assignment = $this->assignment(null, ['status' => AssignmentStatus::SHOWN->value]);
        $this->assignmentSubmission($assignment);

        Sanctum::actingAs($this->lecturerUser());
        $response = $this->getJson("/api/assignment/{$assignment->id}?assignment_submission");
        $response->assertOk()->assertJsonCount(1, 'data.assignment_submissions');

        Sanctum::actingAs($this->studentUser());
        $response = $this->getJson("/api/assignment/{$assignment->id}?assignment_submission");
        $response->assertOk()->assertJsonMissingPath('data.assignment_submissions');
    }
}
