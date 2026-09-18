<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class DepartmentControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function test_admin_can_create_a_department(): void
    {
        Sanctum::actingAs($this->admin());
        $faculty = $this->faculty();

        $response = $this->postJson('/api/department', [
            'name' => 'Department of Computing',
            'faculty_id' => $faculty->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('departments', ['name' => 'Department of Computing', 'faculty_id' => $faculty->id]);
    }

    public function test_creating_a_department_requires_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $faculty = $this->faculty();

        $response = $this->postJson('/api/department', [
            'name' => 'Department of Computing',
            'faculty_id' => $faculty->id,
        ]);

        $response->assertForbidden();
    }

    public function test_store_requires_an_existing_faculty(): void
    {
        Sanctum::actingAs($this->admin());

        $response = $this->postJson('/api/department', [
            'name' => 'Department of Computing',
            'faculty_id' => 999,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('faculty_id');
    }

    public function test_admin_can_update_a_department(): void
    {
        Sanctum::actingAs($this->admin());
        $department = $this->department();

        $response = $this->putJson("/api/department/{$department->id}", [
            'name' => 'Department of Mathematics',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('departments', ['id' => $department->id, 'name' => 'Department of Mathematics']);
    }

    public function test_updating_a_department_requires_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $department = $this->department();

        $response = $this->putJson("/api/department/{$department->id}", [
            'name' => 'Department of Mathematics',
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_delete_a_department(): void
    {
        Sanctum::actingAs($this->admin());
        $department = $this->department();

        $response = $this->deleteJson("/api/department/{$department->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('departments', ['id' => $department->id]);
    }

    public function test_deleting_a_department_requires_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $department = $this->department();

        $response = $this->deleteJson("/api/department/{$department->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('departments', ['id' => $department->id]);
    }
}
