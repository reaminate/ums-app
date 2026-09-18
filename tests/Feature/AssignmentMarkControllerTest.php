<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Notifications\AssignmentGraded;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class AssignmentMarkControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function test_the_teaching_lecturer_can_grade_a_submission(): void
    {
        Notification::fake();
        $lecturer = $this->lecturer();
        $courseOffering = $this->courseOffering($lecturer);
        $assignment = $this->assignment($courseOffering);
        $submission = $this->assignmentSubmission($assignment);

        Sanctum::actingAs($lecturer->user);

        $response = $this->postJson('/api/assignment-mark', [
            'assignment_submission_id' => $submission->id,
            'marks' => 80,
            'comments' => 'Good work',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('assignment_marks', ['assignment_submission_id' => $submission->id]);
        Notification::assertSentTo($submission->student->user, AssignmentGraded::class);
    }

    public function test_grading_a_submission_requires_a_lecturer_or_admin(): void
    {
        $submission = $this->assignmentSubmission();

        Sanctum::actingAs($this->studentUser());

        $response = $this->postJson('/api/assignment-mark', [
            'assignment_submission_id' => $submission->id,
            'marks' => 80,
            'comments' => 'Good work',
        ]);

        $response->assertForbidden();
    }

    public function test_store_requires_marks_to_be_numeric(): void
    {
        $submission = $this->assignmentSubmission();

        Sanctum::actingAs($this->lecturerUser());

        $response = $this->postJson('/api/assignment-mark', [
            'assignment_submission_id' => $submission->id,
            'marks' => 'not-a-number',
            'comments' => 'Good work',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('marks');
    }

    public function test_the_teaching_lecturer_can_update_a_grade(): void
    {
        $lecturer = $this->lecturer();
        $courseOffering = $this->courseOffering($lecturer);
        $assignment = $this->assignment($courseOffering);
        $submission = $this->assignmentSubmission($assignment);
        $assignmentMark = $this->assignmentMark($submission, $lecturer);

        Sanctum::actingAs($lecturer->user);

        $response = $this->putJson("/api/assignment-mark/{$assignmentMark->id}", [
            'assignment_submission_id' => $submission->id,
            'marks' => 90,
        ]);

        $response->assertOk();
    }

    public function test_confirming_an_assignment_mark_update_increments_the_students_grade(): void
    {
        $lecturer = $this->lecturer();
        $courseOffering = $this->courseOffering($lecturer);
        $assignment = $this->assignment($courseOffering);
        $submission = $this->assignmentSubmission($assignment, null, ['status' => SubmissionStatus::ONTIME->value]);
        $assignmentMark = $this->assignmentMark($submission, $lecturer, ['marks' => 50]);
        $grade = $this->grade($submission->student, $courseOffering, [
            'total_assignment_score' => 10,
            'total_test_marks' => 5,
        ]);

        Sanctum::actingAs($lecturer->user);

        $response = $this->putJson("/api/assignment-mark/{$assignmentMark->id}", [
            'assignment_submission_id' => $submission->id,
            'marks' => 20,
            'confirm' => true,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('grades', [
            'id' => $grade->id,
            'total_assignment_score' => 30.0,
            'total_test_marks' => 5.0,
        ]);
    }

    public function test_updating_a_grade_requires_the_teaching_lecturer(): void
    {
        $assignmentMark = $this->assignmentMark();

        Sanctum::actingAs($this->lecturerUser());

        $response = $this->putJson("/api/assignment-mark/{$assignmentMark->id}", [
            'marks' => 90,
        ]);

        $response->assertForbidden();
    }

    public function test_the_teaching_lecturer_can_delete_a_grade(): void
    {
        $lecturer = $this->lecturer();
        $courseOffering = $this->courseOffering($lecturer);
        $assignment = $this->assignment($courseOffering);
        $submission = $this->assignmentSubmission($assignment);
        $assignmentMark = $this->assignmentMark($submission, $lecturer);

        Sanctum::actingAs($lecturer->user);

        $response = $this->deleteJson("/api/assignment-mark/{$assignmentMark->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('assignment_marks', ['id' => $assignmentMark->id]);
    }

    public function test_deleting_a_grade_requires_the_teaching_lecturer(): void
    {
        $assignmentMark = $this->assignmentMark();

        Sanctum::actingAs($this->lecturerUser());

        $response = $this->deleteJson("/api/assignment-mark/{$assignmentMark->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('assignment_marks', ['id' => $assignmentMark->id]);
    }

    public function test_show_only_returns_lecturer_info_to_an_admin(): void
    {
        $assignmentMark = $this->assignmentMark();

        Sanctum::actingAs($this->admin());
        $response = $this->getJson("/api/assignment-mark/{$assignmentMark->id}?lecturer");
        $response->assertOk()->assertJsonPath('data.lecturer_info.staff_number', $assignmentMark->lecturer->staff_number);

        Sanctum::actingAs($assignmentMark->assignmentSubmission->student->user);
        $response = $this->getJson("/api/assignment-mark/{$assignmentMark->id}?lecturer");
        $response->assertOk()->assertJsonMissingPath('data.lecturer_info');
    }
}
