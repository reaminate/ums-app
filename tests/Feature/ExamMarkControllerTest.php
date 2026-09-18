<?php

namespace Tests\Feature;

use App\Events\GradeUpdate;
use App\Notifications\ExamResultPublished;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class ExamMarkControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function test_a_lecturer_can_record_an_exam_mark(): void
    {
        Notification::fake();
        Sanctum::actingAs($this->lecturerUser());
        $courseOffering = $this->courseOffering();
        $exam = $this->exam($courseOffering);
        $student = $this->student();
        $this->enroll($student, $courseOffering);

        $response = $this->postJson('/api/exam-mark', [
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'marks' => 75,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('exam_marks', ['exam_id' => $exam->id, 'student_id' => $student->id]);
        Notification::assertSentTo($student->user, ExamResultPublished::class);
    }

    public function test_recording_an_exam_mark_requires_a_lecturer_or_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $exam = $this->exam();
        $student = $this->student();

        $response = $this->postJson('/api/exam-mark', [
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'marks' => 75,
        ]);

        $response->assertForbidden();
    }

    public function test_store_prevents_duplicate_marks_for_the_same_exam_and_student(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $examMark = $this->examMark();

        $response = $this->postJson('/api/exam-mark', [
            'exam_id' => $examMark->exam_id,
            'student_id' => $examMark->student_id,
            'marks' => 60,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('exam_id');
    }

    public function test_a_lecturer_can_update_an_exam_mark(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $examMark = $this->examMark();

        $response = $this->putJson("/api/exam-mark/{$examMark->id}", [
            'marks' => 85,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('exam_marks', ['id' => $examMark->id, 'marks' => 85]);
    }

    public function test_confirming_an_exam_mark_update_dispatches_a_grade_update(): void
    {
        Event::fake([GradeUpdate::class]);
        Sanctum::actingAs($this->lecturerUser());
        $examMark = $this->examMark();

        $response = $this->putJson("/api/exam-mark/{$examMark->id}", [
            'marks' => 85,
            'confirm' => true,
        ]);

        $response->assertOk();
        Event::assertDispatched(GradeUpdate::class, fn ($event) => $event->exam_mark->is($examMark));
    }

    public function test_confirming_an_exam_mark_update_increments_the_students_grade(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $examMark = $this->examMark();
        $grade = $this->grade($examMark->student, $examMark->exam->courseOffering, [
            'total_assignment_score' => 20,
            'total_test_marks' => 10,
        ]);

        $response = $this->putJson("/api/exam-mark/{$examMark->id}", [
            'marks' => 15,
            'confirm' => true,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('grades', [
            'id' => $grade->id,
            'total_assignment_score' => 20.0,
            'total_test_marks' => 25.0,
        ]);
    }

    public function test_updating_an_exam_mark_requires_a_lecturer_or_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $examMark = $this->examMark();

        $response = $this->putJson("/api/exam-mark/{$examMark->id}", [
            'marks' => 85,
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_delete_an_exam_mark(): void
    {
        Sanctum::actingAs($this->admin());
        $examMark = $this->examMark();

        $response = $this->deleteJson("/api/exam-mark/{$examMark->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('exam_marks', ['id' => $examMark->id]);
    }

    public function test_deleting_an_exam_mark_requires_admin(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $examMark = $this->examMark();

        $response = $this->deleteJson("/api/exam-mark/{$examMark->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('exam_marks', ['id' => $examMark->id]);
    }

    public function test_show_only_returns_full_details_to_an_admin(): void
    {
        $examMark = $this->examMark();

        Sanctum::actingAs($this->admin());
        $response = $this->getJson("/api/exam-mark/{$examMark->id}?exam&student");
        $response->assertOk()->assertJsonPath('data.exam.exam_type', $examMark->exam->exam_type);

        Sanctum::actingAs($examMark->student->user);
        $response = $this->getJson("/api/exam-mark/{$examMark->id}?exam&student");
        $response->assertOk()
            ->assertJsonPath('data.exam', [$examMark->exam->courseOffering->course->name, $examMark->exam->exam_type])
            ->assertJsonPath('data.student', $examMark->student->name);
    }
}
