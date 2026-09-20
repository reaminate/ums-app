<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class GradeControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;


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
