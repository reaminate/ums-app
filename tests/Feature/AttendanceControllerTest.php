<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class AttendanceControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function test_a_lecturer_can_record_attendance(): void
    {
        $lecturer = $this->lecturer();
        $courseOffering = $this->courseOffering($lecturer);
        $classSchedule = $this->classSchedule($courseOffering);
        $student = $this->student();
        $this->enroll($student, $courseOffering);

        Sanctum::actingAs($lecturer->user);

        $response = $this->postJson('/api/attendance', [
            'student_id' => $student->id,
            'class_schedule_id' => $classSchedule->id,
            'attendance_value' => 90,
            'recorded_at' => '08:15',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('attendances', ['student_id' => $student->id, 'class_schedule_id' => $classSchedule->id]);
    }

    public function test_recording_attendance_requires_a_lecturer_or_admin(): void
    {
        $classSchedule = $this->classSchedule();
        $student = $this->student();

        Sanctum::actingAs($this->studentUser());

        $response = $this->postJson('/api/attendance', [
            'student_id' => $student->id,
            'class_schedule_id' => $classSchedule->id,
            'attendance_value' => 90,
            'recorded_at' => '08:15',
        ]);

        $response->assertForbidden();
    }

    public function test_store_requires_a_valid_time_format(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $classSchedule = $this->classSchedule();
        $student = $this->student();

        $response = $this->postJson('/api/attendance', [
            'student_id' => $student->id,
            'class_schedule_id' => $classSchedule->id,
            'attendance_value' => 90,
            'recorded_at' => 'not-a-time',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('recorded_at');
    }

    public function test_store_prevents_duplicate_attendance_for_the_same_student_and_class(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $classSchedule = $this->classSchedule();
        $student = $this->student();
        $this->attendance($classSchedule, $student);

        $response = $this->postJson('/api/attendance', [
            'student_id' => $student->id,
            'class_schedule_id' => $classSchedule->id,
            'attendance_value' => 90,
            'recorded_at' => '08:15',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('class_schedule_id');
    }

    public function test_the_teaching_lecturer_can_update_attendance(): void
    {
        $lecturer = $this->lecturer();
        $courseOffering = $this->courseOffering($lecturer);
        $classSchedule = $this->classSchedule($courseOffering);
        $attendance = $this->attendance($classSchedule);

        Sanctum::actingAs($lecturer->user);

        $response = $this->putJson("/api/attendance/{$attendance->id}", [
            'attendance_value' => 100,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('attendances', ['id' => $attendance->id, 'attendance_value' => 100]);
    }

    public function test_updating_attendance_requires_the_teaching_lecturer_or_admin(): void
    {
        $courseOffering = $this->courseOffering();
        $classSchedule = $this->classSchedule($courseOffering);
        $attendance = $this->attendance($classSchedule);

        Sanctum::actingAs($this->lecturerUser());

        $response = $this->putJson("/api/attendance/{$attendance->id}", [
            'attendance_value' => 100,
        ]);

        $response->assertForbidden();
    }

    public function test_the_teaching_lecturer_can_delete_attendance(): void
    {
        $lecturer = $this->lecturer();
        $courseOffering = $this->courseOffering($lecturer);
        $classSchedule = $this->classSchedule($courseOffering);
        $attendance = $this->attendance($classSchedule);

        Sanctum::actingAs($lecturer->user);

        $response = $this->deleteJson("/api/attendance/{$attendance->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('attendances', ['id' => $attendance->id]);
    }

    public function test_deleting_attendance_requires_the_teaching_lecturer_or_admin(): void
    {
        $courseOffering = $this->courseOffering();
        $classSchedule = $this->classSchedule($courseOffering);
        $attendance = $this->attendance($classSchedule);

        Sanctum::actingAs($this->lecturerUser());

        $response = $this->deleteJson("/api/attendance/{$attendance->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('attendances', ['id' => $attendance->id]);
    }

    public function test_show_only_returns_student_info_to_an_admin(): void
    {
        $lecturer = $this->lecturer();
        $courseOffering = $this->courseOffering($lecturer);
        $classSchedule = $this->classSchedule($courseOffering);
        $student = $this->student();
        $this->enroll($student, $courseOffering);
        $attendance = $this->attendance($classSchedule, $student);

        Sanctum::actingAs($this->admin());
        $response = $this->getJson("/api/attendance/{$attendance->id}?student");
        $response->assertOk()->assertJsonPath('data.student_info.student_number', $student->student_number);

        Sanctum::actingAs($lecturer->user);
        $response = $this->getJson("/api/attendance/{$attendance->id}?student");
        $response->assertOk()->assertJsonMissingPath('data.student_info');
    }
}
