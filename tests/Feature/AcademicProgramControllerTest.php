<?php

namespace Tests\Feature;

use App\Enums\AcademicStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class AcademicProgramControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function test_admin_can_create_an_academic_program(): void
    {
        Sanctum::actingAs($this->admin());
        $department = $this->department();

        $response = $this->postJson('/api/academic-program', [
            'name' => 'diploma in computer science',
            'department_id' => $department->id,
            'qualification_level' => 5,
            'duration' => 3,
            'required_credits' => 3000,
            'status' => AcademicStatus::GOOD_STANDING->value,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('academic_programs', ['name' => 'diploma in computer science']);
    }

    public function test_creating_an_academic_program_requires_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $department = $this->department();

        $response = $this->postJson('/api/academic-program', [
            'name' => 'diploma in computer science',
            'department_id' => $department->id,
            'qualification_level' => 5,
            'duration' => 3,
            'required_credits' => 3000,
            'status' => AcademicStatus::GOOD_STANDING->value,
        ]);

        $response->assertForbidden();
    }

    public function test_store_requires_the_name_to_start_with_a_known_qualification(): void
    {
        Sanctum::actingAs($this->admin());
        $department = $this->department();

        $response = $this->postJson('/api/academic-program', [
            'name' => 'computer science',
            'department_id' => $department->id,
            'qualification_level' => 5,
            'duration' => 3,
            'required_credits' => 3000,
            'status' => AcademicStatus::GOOD_STANDING->value,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('name');
    }

    public function test_admin_can_update_an_academic_program(): void
    {
        Sanctum::actingAs($this->admin());
        $program = $this->academicProgram();

        $response = $this->putJson("/api/academic-program/{$program->id}", [
            'qualification_level' => 7,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('academic_programs', ['id' => $program->id, 'qualification_level' => 7]);
    }

    public function test_updating_an_academic_program_requires_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $program = $this->academicProgram();

        $response = $this->putJson("/api/academic-program/{$program->id}", [
            'qualification_level' => 7,
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_delete_an_academic_program(): void
    {
        Sanctum::actingAs($this->admin());
        $program = $this->academicProgram();

        $response = $this->deleteJson("/api/academic-program/{$program->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('academic_programs', ['id' => $program->id]);
    }

    public function test_deleting_an_academic_program_requires_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $program = $this->academicProgram();

        $response = $this->deleteJson("/api/academic-program/{$program->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('academic_programs', ['id' => $program->id]);
    }

    public function test_show_only_returns_students_to_an_admin(): void
    {
        $program = $this->academicProgram();
        $this->student($program);

        Sanctum::actingAs($this->admin());
        $response = $this->getJson("/api/academic-program/{$program->id}?students");
        $response->assertOk()->assertJsonPath('data.students.0.program_id', $program->id);

        Sanctum::actingAs($this->studentUser());
        $response = $this->getJson("/api/academic-program/{$program->id}?students");
        $response->assertOk()->assertJsonMissingPath('data.students');
    }
}
