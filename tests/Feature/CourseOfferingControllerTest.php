<?php

namespace Tests\Feature;

use App\Enums\CourseOfferingStatus;
use App\Enums\CourseStatus;
use App\Enums\SemesterStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class CourseOfferingControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function test_admin_can_create_a_course_offering(): void
    {
        Sanctum::actingAs($this->admin());
        $course = $this->course(null, ['status' => CourseStatus::OFFERED->value]);
        $semester = $this->academicSemester(['status' => SemesterStatus::REGISTRATION_OPEN->value]);
        $lecturer = $this->lecturer();

        $response = $this->postJson('/api/course-offering', [
            'course_id' => $course->id,
            'semester_id' => $semester->id,
            'lecturer_id' => $lecturer->id,
            'max_students' => 30,
            'status' => CourseOfferingStatus::OPEN->value,
            'start_date' => $semester->start_date,
            'end_date' => $semester->end_date,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('course_offerings', ['course_id' => $course->id, 'lecturer_id' => $lecturer->id]);
    }

    public function test_creating_a_course_offering_requires_admin(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $course = $this->course(null, ['status' => CourseStatus::OFFERED->value]);
        $semester = $this->academicSemester(['status' => SemesterStatus::REGISTRATION_OPEN->value]);
        $lecturer = $this->lecturer();

        $response = $this->postJson('/api/course-offering', [
            'course_id' => $course->id,
            'semester_id' => $semester->id,
            'lecturer_id' => $lecturer->id,
            'max_students' => 30,
            'status' => CourseOfferingStatus::OPEN->value,
            'start_date' => $semester->start_date,
            'end_date' => $semester->end_date,
        ]);

        $response->assertForbidden();
    }

    public function test_store_requires_max_students_to_be_at_least_twenty(): void
    {
        Sanctum::actingAs($this->admin());
        $course = $this->course();
        $semester = $this->academicSemester();
        $lecturer = $this->lecturer();

        $response = $this->postJson('/api/course-offering', [
            'course_id' => $course->id,
            'semester_id' => $semester->id,
            'lecturer_id' => $lecturer->id,
            'max_students' => 5,
            'status' => CourseOfferingStatus::OPEN->value,
            'start_date' => $semester->start_date,
            'end_date' => $semester->end_date,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('max_students');
    }

    public function test_a_lecturer_can_update_a_course_offering(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $courseOffering = $this->courseOffering();

        $response = $this->putJson("/api/course-offering/{$courseOffering->id}", [
            'max_students' => 40,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('course_offerings', ['id' => $courseOffering->id, 'max_students' => 40]);
    }

    public function test_updating_a_course_offering_requires_a_lecturer_or_admin(): void
    {
        Sanctum::actingAs($this->studentUser());
        $courseOffering = $this->courseOffering();

        $response = $this->putJson("/api/course-offering/{$courseOffering->id}", [
            'max_students' => 40,
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_delete_a_course_offering(): void
    {
        Sanctum::actingAs($this->admin());
        $courseOffering = $this->courseOffering();

        $response = $this->deleteJson("/api/course-offering/{$courseOffering->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('course_offerings', ['id' => $courseOffering->id]);
    }

    public function test_deleting_a_course_offering_requires_admin(): void
    {
        Sanctum::actingAs($this->lecturerUser());
        $courseOffering = $this->courseOffering();

        $response = $this->deleteJson("/api/course-offering/{$courseOffering->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('course_offerings', ['id' => $courseOffering->id]);
    }

    public function test_show_only_returns_enrolled_students_to_an_admin(): void
    {
        $courseOffering = $this->courseOffering();
        $student = $this->student();
        $this->enroll($student, $courseOffering);

        Sanctum::actingAs($this->admin());
        $response = $this->getJson("/api/course-offering/{$courseOffering->id}?students");
        $response->assertOk()->assertJsonPath('data.students.0.student_number', $student->student_number);

        Sanctum::actingAs($this->studentUser());
        $response = $this->getJson("/api/course-offering/{$courseOffering->id}?students");
        $response->assertOk()->assertJsonMissingPath('data.students');
    }
}
