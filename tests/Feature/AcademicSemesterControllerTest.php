<?php

namespace Tests\Feature;

use App\Enums\SemesterStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class AcademicSemesterControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Semester A',
            'year' => 2027,
            'start_date' => '2027-01-10',
            'end_date' => '2027-05-10',
            'registration_start_date' => '2026-11-01',
            'registration_end_date' => '2026-12-01',
            'status' => SemesterStatus::REGISTRATION_OPEN->value,
        ], $overrides);
    }

    public function test_admin_can_create_an_academic_semester(): void
    {
        Sanctum::actingAs($this->admin());

        $response = $this->postJson('/api/academic-semester', $this->validPayload());

        $response->assertCreated();
        $this->assertDatabaseHas('academic_semesters', ['name' => 'Semester A']);
    }

    public function test_creating_an_academic_semester_requires_admin(): void
    {
        Sanctum::actingAs($this->studentUser());

        $response = $this->postJson('/api/academic-semester', $this->validPayload());

        $response->assertForbidden();
    }

    public function test_store_requires_the_name_to_start_with_semester(): void
    {
        Sanctum::actingAs($this->admin());

        $response = $this->postJson('/api/academic-semester', $this->validPayload(['name' => 'Fall term']));

        $response->assertUnprocessable()->assertJsonValidationErrors('name');
    }

    public function test_admin_can_update_an_academic_semester(): void
    {
        Sanctum::actingAs($this->admin());
        $semester = $this->academicSemester();

        $response = $this->putJson("/api/academic-semester/{$semester->id}", [
            'status' => SemesterStatus::ONGOING->value,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('academic_semesters', ['id' => $semester->id, 'status' => SemesterStatus::ONGOING->value]);
    }

    public function test_updating_an_academic_semester_requires_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $semester = $this->academicSemester();

        $response = $this->putJson("/api/academic-semester/{$semester->id}", [
            'status' => SemesterStatus::ONGOING->value,
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_delete_an_academic_semester(): void
    {
        Sanctum::actingAs($this->admin());
        $semester = $this->academicSemester();

        $response = $this->deleteJson("/api/academic-semester/{$semester->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('academic_semesters', ['id' => $semester->id]);
    }

    public function test_deleting_an_academic_semester_requires_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $semester = $this->academicSemester();

        $response = $this->deleteJson("/api/academic-semester/{$semester->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('academic_semesters', ['id' => $semester->id]);
    }

    public function test_show_only_returns_full_course_offering_details_to_an_admin(): void
    {
        $semester = $this->academicSemester();
        $this->courseOffering(null, null, $semester);

        Sanctum::actingAs($this->admin());
        $response = $this->getJson("/api/academic-semester/{$semester->id}?course_offerings");
        $response->assertOk()->assertJsonStructure(['data' => ['course_offerings' => [['course_id']]]]);

        Sanctum::actingAs($this->studentUser());
        $response = $this->getJson("/api/academic-semester/{$semester->id}?course_offerings");
        $response->assertOk()->assertJsonMissing(['course_id']);
    }
}
