<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class ExamControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function test_a_lecturer_can_create_an_exam(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $courseOffering = $this->courseOffering();

        $response = $this->postJson('/api/exam', [
            'course_offering_id' => $courseOffering->id,
            'exam_type' => 'final',
            'exam_date' => now()->addWeek()->toDateString(),
            'max_marks' => 100,
            'weight' => 50,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('exams', ['course_offering_id' => $courseOffering->id, 'exam_type' => 'final']);
    }

    public function test_creating_an_exam_requires_a_lecturer_or_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $courseOffering = $this->courseOffering();

        $response = $this->postJson('/api/exam', [
            'course_offering_id' => $courseOffering->id,
            'exam_type' => 'final',
            'exam_date' => now()->addWeek()->toDateString(),
            'max_marks' => 100,
            'weight' => 50,
        ]);

        $response->assertForbidden();
    }

    public function test_store_requires_weight_to_be_between_zero_and_a_hundred(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $courseOffering = $this->courseOffering();

        $response = $this->postJson('/api/exam', [
            'course_offering_id' => $courseOffering->id,
            'exam_type' => 'final',
            'exam_date' => now()->addWeek()->toDateString(),
            'max_marks' => 100,
            'weight' => 150,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('weight');
    }

    public function test_a_lecturer_can_update_an_exam(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $exam = $this->exam();

        $response = $this->putJson("/api/exam/{$exam->id}", [
            'weight' => 60,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('exams', ['id' => $exam->id, 'weight' => 60]);
    }

    public function test_updating_an_exam_requires_a_lecturer_or_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $exam = $this->exam();

        $response = $this->putJson("/api/exam/{$exam->id}", [
            'weight' => 60,
        ]);

        $response->assertForbidden();
    }

    public function test_a_lecturer_can_delete_an_exam(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $exam = $this->exam();

        $response = $this->deleteJson("/api/exam/{$exam->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('exams', ['id' => $exam->id]);
    }

    public function test_deleting_an_exam_requires_a_lecturer_or_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $exam = $this->exam();

        $response = $this->deleteJson("/api/exam/{$exam->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('exams', ['id' => $exam->id]);
    }

    public function test_show_only_returns_full_course_offering_details_to_an_admin(): void
    {
        $courseOffering = $this->courseOffering();
        $exam = $this->exam($courseOffering);
        $student = $this->student();
        $this->enroll($student, $courseOffering);

        Sanctum::actingAs($this->admin());
        $response = $this->getJson("/api/exam/{$exam->id}?course_offering");
        $response->assertOk()->assertJsonStructure(['data' => ['course' => ['course_id']]]);

        Sanctum::actingAs($student->user);
        $response = $this->getJson("/api/exam/{$exam->id}?course_offering");
        $response->assertOk()->assertJsonPath('data.course', $courseOffering->course->code);
    }
}
