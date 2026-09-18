<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class GradeControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function test_a_lecturer_can_record_a_grade(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $courseOffering = $this->courseOffering();
        $student = $this->student();
        $this->enroll($student, $courseOffering);

        $response = $this->postJson('/api/grade', [
            'student_id' => $student->id,
            'course_offering_id' => $courseOffering->id,
            'total_assignment_score' => '30.00',
            'total_test_marks' => '40.00',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('grades', ['student_id' => $student->id, 'course_offering_id' => $courseOffering->id]);
    }

    public function test_recording_a_grade_requires_a_lecturer_or_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $courseOffering = $this->courseOffering();
        $student = $this->student();

        $response = $this->postJson('/api/grade', [
            'student_id' => $student->id,
            'course_offering_id' => $courseOffering->id,
            'total_assignment_score' => '30.00',
            'total_test_marks' => '40.00',
        ]);

        $response->assertForbidden();
    }

    public function test_store_requires_the_scores_to_have_two_decimal_places(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $courseOffering = $this->courseOffering();
        $student = $this->student();

        $response = $this->postJson('/api/grade', [
            'student_id' => $student->id,
            'course_offering_id' => $courseOffering->id,
            'total_assignment_score' => '30.001',
            'total_test_marks' => '40.00',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('total_assignment_score');
    }

    public function test_a_lecturer_can_update_a_grade(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $grade = $this->grade();

        $response = $this->putJson("/api/grade/{$grade->id}", [
            'total_test_marks' => '50.00',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('grades', ['id' => $grade->id, 'total_test_marks' => 50.0]);
    }

    public function test_updating_a_grade_requires_a_lecturer_or_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $grade = $this->grade();

        $response = $this->putJson("/api/grade/{$grade->id}", [
            'total_test_marks' => '50.00',
        ]);

        $response->assertForbidden();
    }

    public function test_a_lecturer_can_delete_a_grade(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $grade = $this->grade();

        $response = $this->deleteJson("/api/grade/{$grade->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('grades', ['id' => $grade->id]);
    }

    public function test_deleting_a_grade_requires_a_lecturer_or_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $grade = $this->grade();

        $response = $this->deleteJson("/api/grade/{$grade->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('grades', ['id' => $grade->id]);
    }

    public function test_show_only_returns_student_info_to_an_admin(): void
    {
        $courseOffering = $this->courseOffering();
        $student = $this->student();
        $this->enroll($student, $courseOffering);
        $grade = $this->grade($student, $courseOffering);

        Sanctum::actingAs($this->admin());
        $response = $this->getJson("/api/grade/{$grade->id}?student");
        $response->assertOk()->assertJsonPath('data.student_info.student_number', $student->student_number);

        Sanctum::actingAs($student->user);
        $response = $this->getJson("/api/grade/{$grade->id}?student");
        $response->assertOk()->assertJsonMissingPath('data.student_info');
    }
}
