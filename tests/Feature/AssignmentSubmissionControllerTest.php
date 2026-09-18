<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class AssignmentSubmissionControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function test_a_student_can_submit_an_assignment(): void
    {
        Storage::fake('public');
        $courseOffering = $this->courseOffering();
        $assignment = $this->assignment($courseOffering);
        $student = $this->student();
        $this->enroll($student, $courseOffering);

        Sanctum::actingAs($student->user);

        $response = $this->postJson('/api/assignment-submission', [
            'assignment_id' => $assignment->id,
            'file' => UploadedFile::fake()->create('submission.pdf', 100, 'application/pdf'),
            'comments' => 'Here is my work',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('assignment_submissions', ['assignment_id' => $assignment->id, 'student_id' => $student->id]);
    }

    public function test_store_requires_a_pdf_or_docx_file(): void
    {
        Storage::fake('public');
        $assignment = $this->assignment();

        Sanctum::actingAs($this->studentUser());

        $response = $this->postJson('/api/assignment-submission', [
            'assignment_id' => $assignment->id,
            'file' => UploadedFile::fake()->create('submission.exe', 100, 'application/octet-stream'),
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('file');
    }

    public function test_the_owning_student_can_update_a_submission(): void
    {
        Storage::fake('public');
        $submission = $this->assignmentSubmission();

        Sanctum::actingAs($submission->student->user);

        $response = $this->putJson("/api/assignment-submission/{$submission->id}", [
            'comments' => 'Updated comments',
        ]);

        $response->assertOk();
    }

    public function test_updating_a_submission_requires_the_owning_student_or_a_lecturer(): void
    {
        $submission = $this->assignmentSubmission();

        Sanctum::actingAs($this->studentUser());

        $response = $this->putJson("/api/assignment-submission/{$submission->id}", [
            'comments' => 'Updated comments',
        ]);

        $response->assertForbidden();
    }

    public function test_the_owning_student_can_delete_a_submission(): void
    {
        $submission = $this->assignmentSubmission();

        Sanctum::actingAs($submission->student->user);

        $response = $this->deleteJson("/api/assignment-submission/{$submission->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('assignment_submissions', ['id' => $submission->id]);
    }

    public function test_deleting_a_submission_requires_the_owning_student_or_a_lecturer(): void
    {
        $submission = $this->assignmentSubmission();

        Sanctum::actingAs($this->studentUser());

        $response = $this->deleteJson("/api/assignment-submission/{$submission->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('assignment_submissions', ['id' => $submission->id]);
    }

    public function test_show_only_returns_file_and_assignment_details_to_an_admin(): void
    {
        $submission = $this->assignmentSubmission();

        Sanctum::actingAs($this->admin());
        $response = $this->getJson("/api/assignment-submission/{$submission->id}");
        $response->assertOk()->assertJsonPath('data.file_more_info.file_path', $submission->file_path);

        Sanctum::actingAs($submission->student->user);
        $response = $this->getJson("/api/assignment-submission/{$submission->id}");
        $response->assertOk()
            ->assertJsonMissingPath('data.file_more_info')
            ->assertJsonMissingPath('data.assignment_more_info');
    }
}
