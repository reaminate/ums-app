<?php

namespace Tests\Feature;

use App\Enums\DaysOfTheWeek;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class ClassScheduleControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function test_admin_can_create_a_class_schedule(): void
    {
        Sanctum::actingAs($this->admin());
        $courseOffering = $this->courseOffering();

        $response = $this->postJson('/api/class-schedule', [
            'course_offering_id' => $courseOffering->id,
            'day' => DaysOfTheWeek::MONDAY->value,
            'start_time' => '10:00',
            'end_time' => '11:30',
            'room_number' => 'Room 202',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('class_schedules', ['course_offering_id' => $courseOffering->id, 'room_number' => 'Room 202']);
    }

    public function test_creating_a_class_schedule_requires_admin(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $courseOffering = $this->courseOffering();

        $response = $this->postJson('/api/class-schedule', [
            'course_offering_id' => $courseOffering->id,
            'day' => DaysOfTheWeek::MONDAY->value,
            'start_time' => '10:00',
            'end_time' => '11:30',
            'room_number' => 'Room 202',
        ]);

        $response->assertForbidden();
    }

    public function test_store_rejects_an_end_time_before_the_start_time(): void
    {
        Sanctum::actingAs($this->admin());
        $courseOffering = $this->courseOffering();

        $response = $this->postJson('/api/class-schedule', [
            'course_offering_id' => $courseOffering->id,
            'day' => DaysOfTheWeek::MONDAY->value,
            'start_time' => '11:30',
            'end_time' => '10:00',
            'room_number' => 'Room 202',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('end_time');
    }

    public function test_store_rejects_a_room_already_booked_for_the_same_time_slot(): void
    {
        Sanctum::actingAs($this->admin());
        $courseOffering = $this->courseOffering();
        $this->classSchedule($courseOffering, [
            'day' => DaysOfTheWeek::MONDAY->value,
            'start_time' => '10:00',
            'end_time' => '11:30',
            'room_number' => 'Room 202',
        ]);

        $otherOffering = $this->courseOffering();
        $response = $this->postJson('/api/class-schedule', [
            'course_offering_id' => $otherOffering->id,
            'day' => DaysOfTheWeek::MONDAY->value,
            'start_time' => '10:30',
            'end_time' => '12:00',
            'room_number' => 'Room 202',
        ]);

        $response->assertStatus(422)->assertJson(['message' => 'time slot is already booked']);
    }

    public function test_admin_can_update_a_class_schedule(): void
    {
        Sanctum::actingAs($this->admin());
        $classSchedule = $this->classSchedule();

        $response = $this->putJson("/api/class-schedule/{$classSchedule->id}", [
            'room_number' => 'Room 303',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('class_schedules', ['id' => $classSchedule->id, 'room_number' => 'Room 303']);
    }

    public function test_updating_a_class_schedule_requires_admin(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $classSchedule = $this->classSchedule();

        $response = $this->putJson("/api/class-schedule/{$classSchedule->id}", [
            'room_number' => 'Room 303',
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_delete_a_class_schedule(): void
    {
        Sanctum::actingAs($this->admin());
        $classSchedule = $this->classSchedule();

        $response = $this->deleteJson("/api/class-schedule/{$classSchedule->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('class_schedules', ['id' => $classSchedule->id]);
    }

    public function test_deleting_a_class_schedule_requires_admin(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $classSchedule = $this->classSchedule();

        $response = $this->deleteJson("/api/class-schedule/{$classSchedule->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('class_schedules', ['id' => $classSchedule->id]);
    }

    public function test_show_only_returns_attendances_to_an_admin(): void
    {
        $classSchedule = $this->classSchedule();
        $this->attendance($classSchedule);

        Sanctum::actingAs($this->admin());
        $response = $this->getJson("/api/class-schedule/{$classSchedule->id}?attendances");
        $response->assertOk()->assertJsonCount(1, 'data.attendances');

        Sanctum::actingAs($this->studentUser());
        $response = $this->getJson("/api/class-schedule/{$classSchedule->id}?attendances");
        $response->assertForbidden();
    }
}
