<?php

namespace Tests\Feature;

use App\Enums\LecturerStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class LecturerControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function test_a_lecturer_can_update_their_own_profile(): void
    {
        $lecturer = $this->lecturer();

        Sanctum::actingAs($lecturer->user);

        $response = $this->putJson("/api/lecturer/{$lecturer->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('lecturers', ['id' => $lecturer->id, 'name' => 'Updated Name']);
        $this->assertDatabaseHas('users', ['id' => $lecturer->user_id, 'name' => 'Updated Name']);
    }

    public function test_updating_a_lecturer_requires_admin_or_the_lecturer_themselves(): void
    {
        $lecturer = $this->lecturer();

        Sanctum::actingAs($this->lecturerUser());

        $response = $this->putJson("/api/lecturer/{$lecturer->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertForbidden();
    }

    public function test_a_lecturer_teaching_an_active_course_cannot_go_on_leave(): void
    {
        $lecturer = $this->lecturer();
        $semester = $this->academicSemester();
        $this->courseOffering($lecturer, null, $semester, ['end_date' => now()->addMonth()->toDateString()]);

        Sanctum::actingAs($lecturer->user);

        $response = $this->putJson("/api/lecturer/{$lecturer->id}", [
            'status' => LecturerStatus::ONLEAVE->value,
        ]);

        $response->assertOk()->assertJson(['message' => 'You cannot go on leave as youre still teaching']);
        $this->assertDatabaseHas('lecturers', ['id' => $lecturer->id, 'status' => LecturerStatus::AVAILABLE->value]);
    }

    public function test_admin_can_delete_a_lecturer(): void
    {
        Sanctum::actingAs($this->admin());
        $lecturer = $this->lecturer();

        $response = $this->deleteJson("/api/lecturer/{$lecturer->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('lecturers', ['id' => $lecturer->id]);
    }

    public function test_deleting_a_lecturer_requires_admin(): void
    {
        $lecturer = $this->lecturer();

        Sanctum::actingAs($lecturer->user);

        $response = $this->deleteJson("/api/lecturer/{$lecturer->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('lecturers', ['id' => $lecturer->id]);
    }

    public function test_show_only_returns_user_info_to_an_admin(): void
    {
        $lecturer = $this->lecturer();

        Sanctum::actingAs($this->admin());
        $response = $this->getJson("/api/lecturer/{$lecturer->id}?user");
        $response->assertOk()->assertJsonPath('data.user_more_info.email', $lecturer->user->email);

        Sanctum::actingAs($lecturer->user);
        $response = $this->getJson("/api/lecturer/{$lecturer->id}?user");
        $response->assertOk()->assertJsonMissingPath('data.user_more_info');
    }
}
