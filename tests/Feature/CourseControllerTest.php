<?php

namespace Tests\Feature;

use App\Enums\CourseStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class CourseControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function test_admin_can_create_a_course(): void
    {
        Sanctum::actingAs($this->admin());
        $department = $this->department();

        $response = $this->postJson('/api/course', [
            'name' => 'Introduction To Programming',
            'description' => 'Learn the basics of programming',
            'department_id' => $department->id,
            'credit_value' => 300,
            'course_level' => 1,
            'status' => CourseStatus::OFFERED->value,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('courses', ['name' => 'Introduction To Programming']);
    }

    public function test_admin_can_create_a_course_with_prerequisites(): void
    {
        Sanctum::actingAs($this->admin());
        $department = $this->department();
        $prerequisite = $this->course($department, ['course_level' => 1]);

        $response = $this->postJson('/api/course', [
            'name' => 'Advanced Programming',
            'description' => 'Builds on the basics',
            'department_id' => $department->id,
            'credit_value' => 300,
            'course_level' => 2,
            'course_prerequisite' => [$prerequisite->id],
            'status' => CourseStatus::OFFERED->value,
        ]);

        $response->assertCreated();
        $course = $department->courses()->where('name', 'Advanced Programming')->firstOrFail();
        $this->assertTrue($course->prerequisites->contains($prerequisite));
    }

    public function test_creating_a_course_requires_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $department = $this->department();

        $response = $this->postJson('/api/course', [
            'name' => 'Introduction To Programming',
            'description' => 'Learn the basics of programming',
            'department_id' => $department->id,
            'credit_value' => 300,
            'course_level' => 1,
            'status' => CourseStatus::OFFERED->value,
        ]);

        $response->assertForbidden();
    }

    public function test_store_requires_a_unique_name(): void
    {
        Sanctum::actingAs($this->admin());
        $department = $this->department();
        $this->course($department, ['name' => 'Introduction To Programming']);

        $response = $this->postJson('/api/course', [
            'name' => 'Introduction To Programming',
            'description' => 'Learn the basics of programming',
            'department_id' => $department->id,
            'credit_value' => 300,
            'course_level' => 1,
            'status' => CourseStatus::OFFERED->value,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('name');
    }

    public function test_admin_can_update_a_course(): void
    {
        Sanctum::actingAs($this->admin());
        $course = $this->course();

        $response = $this->putJson("/api/course/{$course->id}", [
            'description' => 'Updated description',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('courses', ['id' => $course->id, 'description' => 'Updated description']);
    }

    public function test_updating_a_course_requires_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $course = $this->course();

        $response = $this->putJson("/api/course/{$course->id}", [
            'description' => 'Updated description',
        ]);

        $response->assertForbidden();
    }

    public function test_a_course_cannot_be_its_own_prerequisite(): void
    {
        Sanctum::actingAs($this->admin());
        $course = $this->course(null, ['course_level' => 2]);

        $response = $this->putJson("/api/course/{$course->id}", [
            'course_prerequisite' => [$course->id],
        ]);

        $response->assertStatus(401);
    }

    public function test_admin_can_delete_a_course(): void
    {
        Sanctum::actingAs($this->admin());
        $course = $this->course();

        $response = $this->deleteJson("/api/course/{$course->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_deleting_a_course_requires_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $course = $this->course();

        $response = $this->deleteJson("/api/course/{$course->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('courses', ['id' => $course->id]);
    }
}
