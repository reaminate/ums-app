<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class FacultyControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function test_admin_can_create_a_faculty(): void
    {
        Sanctum::actingAs($this->admin());

        $response = $this->postJson('/api/faculty', [
            'name' => 'Faculty of Science',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('faculties', ['name' => 'Faculty of Science']);
    }

    public function test_creating_a_faculty_requires_admin(): void
    {
        Sanctum::actingAs($this->studentUser());

        $response = $this->postJson('/api/faculty', [
            'name' => 'Faculty of Science',
        ]);

        $response->assertForbidden();
    }

    public function test_store_requires_a_unique_name(): void
    {
        Sanctum::actingAs($this->admin());
        $this->faculty(['name' => 'Faculty of Science']);

        $response = $this->postJson('/api/faculty', [
            'name' => 'Faculty of Science',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('name');
    }

    public function test_admin_can_update_a_faculty(): void
    {
        Sanctum::actingAs($this->admin());
        $faculty = $this->faculty();

        $response = $this->putJson("/api/faculty/{$faculty->id}", [
            'name' => 'Faculty of Engineering',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('faculties', ['id' => $faculty->id, 'name' => 'Faculty of Engineering']);
    }

    public function test_updating_a_faculty_requires_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $faculty = $this->faculty();

        $response = $this->putJson("/api/faculty/{$faculty->id}", [
            'name' => 'Faculty of Engineering',
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_delete_a_faculty(): void
    {
        Sanctum::actingAs($this->admin());
        $faculty = $this->faculty();

        $response = $this->deleteJson("/api/faculty/{$faculty->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('faculties', ['id' => $faculty->id]);
    }

    public function test_deleting_a_faculty_requires_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $faculty = $this->faculty();

        $response = $this->deleteJson("/api/faculty/{$faculty->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('faculties', ['id' => $faculty->id]);
    }
}
