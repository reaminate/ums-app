<?php

namespace Tests\Feature;

use App\Enums\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function test_admin_can_create_a_student_user_and_a_student_profile_is_created(): void
    {
        Notification::fake();
        Sanctum::actingAs($this->admin());
        $program = $this->academicProgram();

        $response = $this->postJson('/api/user', [
            'name' => 'Jamie Student',
            'type' => UserType::STUDENT->value,
            'email' => 'jamie@example.com',
            'password' => 'password123',
            'is_active' => true,
            'program_id' => $program->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('users', ['email' => 'jamie@example.com', 'type' => UserType::STUDENT->value]);
        $this->assertDatabaseHas('students', ['email' => 'jamie@example.com', 'program_id' => $program->id]);
    }

    public function test_admin_can_create_a_lecturer_user_and_a_lecturer_profile_is_created(): void
    {
        Notification::fake();
        Sanctum::actingAs($this->admin());
        $department = $this->department();

        $response = $this->postJson('/api/user', [
            'name' => 'Jamie Lecturer',
            'type' => UserType::LECTURER->value,
            'email' => 'jamie.lecturer@example.com',
            'password' => 'password123',
            'is_active' => true,
            'department_id' => $department->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('users', ['email' => 'jamie.lecturer@example.com', 'type' => UserType::LECTURER->value]);
        $this->assertDatabaseHas('lecturers', ['email' => 'jamie.lecturer@example.com', 'department_id' => $department->id]);
    }

    public function test_creating_a_user_requires_admin(): void
    {
        Sanctum::actingAs($this->studentUser());

        $response = $this->postJson('/api/user', [
            'name' => 'Jamie Student',
            'type' => UserType::STUDENT->value,
            'email' => 'jamie@example.com',
            'password' => 'password123',
            'is_active' => true,
            'program_id' => $this->academicProgram()->id,
        ]);

        $response->assertForbidden();
    }

    public function test_store_requires_a_program_when_creating_a_student(): void
    {
        Sanctum::actingAs($this->admin());

        $response = $this->postJson('/api/user', [
            'name' => 'Jamie Student',
            'type' => UserType::STUDENT->value,
            'email' => 'jamie@example.com',
            'password' => 'password123',
            'is_active' => true,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('program_id');
    }

    public function test_store_requires_a_unique_email(): void
    {
        Sanctum::actingAs($this->admin());
        $this->admin(['email' => 'taken@example.com']);

        $response = $this->postJson('/api/user', [
            'name' => 'Jamie',
            'type' => UserType::ADMIN->value,
            'email' => 'taken@example.com',
            'password' => 'password123',
            'is_active' => true,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_a_user_can_update_their_own_profile(): void
    {
        $user = $this->studentUser();
        Sanctum::actingAs($user);

        $response = $this->putJson("/api/user/{$user->id}", [
            'name' => 'New Name',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name']);
    }

    public function test_a_user_cannot_update_another_users_profile(): void
    {
        $user = $this->studentUser();
        Sanctum::actingAs($this->studentUser());

        $response = $this->putJson("/api/user/{$user->id}", [
            'name' => 'New Name',
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_delete_a_user(): void
    {
        Sanctum::actingAs($this->admin());
        $user = $this->studentUser();

        $response = $this->deleteJson("/api/user/{$user->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_deleting_a_user_requires_admin(): void
    {
        $user = $this->studentUser();
        Sanctum::actingAs($this->studentUser());

        $response = $this->deleteJson("/api/user/{$user->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_show_only_returns_the_type_and_full_student_details_to_an_admin(): void
    {
        $program = $this->academicProgram();
        $user = $this->studentUser();
        $student = $this->student($program, ['user_id' => $user->id]);

        Sanctum::actingAs($this->admin());
        $response = $this->getJson("/api/user/{$user->id}?student");
        $response->assertOk()
            ->assertJsonPath('data.type', UserType::STUDENT->value)
            ->assertJsonPath('data.student_number.student_number', $student->student_number);

        Sanctum::actingAs($user);
        $response = $this->getJson("/api/user/{$user->id}?student");
        $response->assertOk()
            ->assertJsonMissingPath('data.type')
            ->assertJsonPath('data.student_number', $student->student_number);
    }
}
