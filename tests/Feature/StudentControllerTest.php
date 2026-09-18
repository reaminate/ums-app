<?php

namespace Tests\Feature;

use App\Enums\CourseOfferingStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\SemesterStatus;
use App\Enums\StudentStatus;
use App\Notifications\EnrollmentStatusUpdate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class StudentControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function test_a_student_can_update_their_own_profile(): void
    {
        $student = $this->student();

        Sanctum::actingAs($student->user);

        $response = $this->putJson("/api/student/{$student->id}", [
            'status' => StudentStatus::PROBATION->value,
            'email' => 'updated.student@example.com',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'status' => StudentStatus::PROBATION->value,
            'email' => 'updated.student@example.com',
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $student->user_id,
            'email' => 'updated.student@example.com',
        ]);
    }

    public function test_updating_a_student_requires_admin_or_the_student_themselves(): void
    {
        $student = $this->student();

        Sanctum::actingAs($this->studentUser());

        $response = $this->putJson("/api/student/{$student->id}", [
            'status' => StudentStatus::PROBATION->value,
        ]);

        $response->assertForbidden();
    }

    public function test_update_can_enroll_the_student_into_open_course_offerings(): void
    {
        $student = $this->student();
        $courseOffering = $this->courseOffering(null, null, null, ['status' => CourseOfferingStatus::OPEN->value]);

        Sanctum::actingAs($student->user);

        $response = $this->putJson("/api/student/{$student->id}", [
            'course_offerings' => [$courseOffering->id],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('enrollment', ['student_id' => $student->id, 'course_offering_id' => $courseOffering->id]);
    }

    public function test_update_rejects_two_offerings_of_the_same_course(): void
    {
        $student = $this->student();
        $course = $this->course();
        $semester = $this->academicSemester();
        $offeringOne = $this->courseOffering(null, $course, $semester, ['status' => CourseOfferingStatus::OPEN->value]);
        $offeringTwo = $this->courseOffering(null, $course, $semester, ['status' => CourseOfferingStatus::OPEN->value]);

        Sanctum::actingAs($student->user);

        $response = $this->putJson("/api/student/{$student->id}", [
            'course_offerings' => [$offeringOne->id, $offeringTwo->id],
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('course_offerings.1');
    }

    public function test_admin_can_delete_a_student(): void
    {
        Sanctum::actingAs($this->admin());
        $student = $this->student();

        $response = $this->deleteJson("/api/student/{$student->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('students', ['id' => $student->id]);
    }

    public function test_deleting_a_student_requires_admin(): void
    {
        $student = $this->student();

        Sanctum::actingAs($student->user);

        $response = $this->deleteJson("/api/student/{$student->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('students', ['id' => $student->id]);
    }

    public function test_admin_can_enroll_a_student(): void
    {
        Notification::fake();
        Sanctum::actingAs($this->admin());
        $student = $this->student();
        $courseOffering = $this->courseOffering();
        $this->enroll($student, $courseOffering, EnrollmentStatus::PROCESSING->value);

        $response = $this->putJson("/api/student/{$student->id}/enroll");

        $response->assertOk()->assertJson(['message' => 'success']);
        $this->assertDatabaseHas('enrollment', [
            'student_id' => $student->id,
            'course_offering_id' => $courseOffering->id,
            'status' => EnrollmentStatus::ENROLLED->value,
        ]);
        $this->assertDatabaseMissing('enrollment', [
            'student_id' => $student->id,
            'course_offering_id' => $courseOffering->id,
            'enrolled_at' => null,
        ]);
        Notification::assertSentTo($student->user, EnrollmentStatusUpdate::class);
    }

    public function test_enrolling_a_student_fails_once_the_course_offering_is_full(): void
    {
        Notification::fake();
        $courseOffering = $this->courseOffering(null, null, null, ['max_students' => 1]);

        $enrolledStudent = $this->student();
        $this->enroll($enrolledStudent, $courseOffering, EnrollmentStatus::ENROLLED->value);

        $pendingStudent = $this->student();
        $this->enroll($pendingStudent, $courseOffering, EnrollmentStatus::PROCESSING->value);

        Sanctum::actingAs($this->admin());
        $response = $this->putJson("/api/student/{$pendingStudent->id}/enroll");

        $response->assertOk()->assertJson(['message' => 'failed_some']);
        $this->assertDatabaseHas('enrollment', [
            'student_id' => $pendingStudent->id,
            'course_offering_id' => $courseOffering->id,
            'status' => EnrollmentStatus::PROCESSING->value,
            'enrolled_at' => null,
        ]);
        Notification::assertSentTo($pendingStudent->user, EnrollmentStatusUpdate::class);
    }

    public function test_enrolling_a_student_requires_admin(): void
    {
        $student = $this->student();

        Sanctum::actingAs($student->user);

        $response = $this->putJson("/api/student/{$student->id}/enroll");

        $response->assertForbidden();
    }

    public function test_show_only_returns_user_info_to_an_admin(): void
    {
        $student = $this->student();

        Sanctum::actingAs($this->admin());
        $response = $this->getJson("/api/student/{$student->id}?user");
        $response->assertOk()->assertJsonPath('data.user_more_info.email', $student->user->email);

        Sanctum::actingAs($student->user);
        $response = $this->getJson("/api/student/{$student->id}?user");
        $response->assertOk()->assertJsonMissingPath('data.user_more_info');
    }

    public function test_show_only_returns_past_semester_attendance_to_an_admin(): void
    {
        $student = $this->student();
        $ongoingSemester = $this->academicSemester(['status' => SemesterStatus::ONGOING->value]);
        $pastSemester = $this->academicSemester(['status' => SemesterStatus::FINISHED->value]);

        $ongoingOffering = $this->courseOffering(null, null, $ongoingSemester);
        $pastOffering = $this->courseOffering(null, null, $pastSemester);

        $this->attendance($this->classSchedule($ongoingOffering), $student);
        $this->attendance($this->classSchedule($pastOffering), $student);

        Sanctum::actingAs($this->admin());
        $response = $this->getJson("/api/student/{$student->id}?attendances");
        $response->assertOk()->assertJsonCount(2, 'data.attendance_per_class');

        Sanctum::actingAs($student->user);
        $response = $this->getJson("/api/student/{$student->id}?attendances");
        $response->assertOk()->assertJsonCount(1, 'data.attendance_per_class');
    }
}
