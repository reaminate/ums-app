<?php

namespace Tests\Feature\Concerns;

use App\Enums\AssignmentStatus;
use App\Enums\CourseOfferingStatus;
use App\Enums\DaysOfTheWeek;
use App\Enums\EnrollmentStatus;
use App\Enums\LecturerStatus;
use App\Enums\StudentStatus;
use App\Enums\SubmissionStatus;
use App\Enums\UserType;
use App\Models\AcademicProgram;
use App\Models\AcademicSemester;
use App\Models\Assignment;
use App\Models\AssignmentMark;
use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\ClassSchedule;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\UploadedFile;

trait CreatesTestData
{
    protected function admin(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'type' => UserType::ADMIN->value,
            'is_active' => true,
        ], $attributes));
    }

    protected function studentUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'type' => UserType::STUDENT->value,
            'is_active' => true,
        ], $attributes));
    }

    protected function lecturerUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'type' => UserType::LECTURER->value,
            'is_active' => true,
        ], $attributes));
    }

    protected function faculty(array $attributes = []): Faculty
    {
        return Faculty::factory()->create($attributes);
    }

    protected function department(?Faculty $faculty = null, array $attributes = []): Department
    {
        $faculty ??= $this->faculty();

        return Department::factory()->create(array_merge([
            'faculty_id' => $faculty->id,
        ], $attributes));
    }

    protected function academicProgram(?Department $department = null, array $attributes = []): AcademicProgram
    {
        $department ??= $this->department();

        return AcademicProgram::factory()->create(array_merge([
            'department_id' => $department->id,
        ], $attributes));
    }

    protected function course(?Department $department = null, array $attributes = []): Course
    {
        $department ??= $this->department();

        return Course::factory()->create(array_merge([
            'department_id' => $department->id,
        ], $attributes));
    }

    protected function academicSemester(array $attributes = []): AcademicSemester
    {
        return AcademicSemester::factory()->create($attributes);
    }

    /**
     * Bypasses StudentFactory (which picks a random existing student-type
     * user via a faker ->unique() call that is not reset between tests).
     */
    protected function student(?AcademicProgram $program = null, array $attributes = []): Student
    {
        $program ??= $this->academicProgram();
        $user = isset($attributes['user_id']) ? User::find($attributes['user_id']) : $this->studentUser();

        return Student::create(array_merge([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'program_id' => $program->id,
            'status' => StudentStatus::ENROLLED->value,
        ], $attributes));
    }

    /**
     * Bypasses LecturerFactory for the same reason as student().
     */
    protected function lecturer(?Department $department = null, array $attributes = []): Lecturer
    {
        $department ??= $this->department();
        $user = isset($attributes['user_id']) ? User::find($attributes['user_id']) : $this->lecturerUser();

        return Lecturer::create(array_merge([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'department_id' => $department->id,
            'status' => LecturerStatus::AVAILABLE->value,
        ], $attributes));
    }

    protected function courseOffering(?Lecturer $lecturer = null, ?Course $course = null, ?AcademicSemester $semester = null, array $attributes = []): CourseOffering
    {
        $semester ??= $this->academicSemester();
        $course ??= $this->course();
        $lecturer ??= $this->lecturer();

        return CourseOffering::create(array_merge([
            'course_id' => $course->id,
            'semester_id' => $semester->id,
            'lecturer_id' => $lecturer->id,
            'max_students' => 30,
            'status' => CourseOfferingStatus::OPEN->value,
            'start_date' => $semester->start_date,
            'end_date' => $semester->end_date,
        ], $attributes));
    }

    protected function classSchedule(?CourseOffering $courseOffering = null, array $attributes = []): ClassSchedule
    {
        $courseOffering ??= $this->courseOffering();

        return ClassSchedule::create(array_merge([
            'course_offering_id' => $courseOffering->id,
            'day' => DaysOfTheWeek::MONDAY->value,
            'start_time' => '08:00',
            'end_time' => '09:30',
            'room_number' => 'Room 101',
        ], $attributes));
    }

    protected function enroll(Student $student, CourseOffering $courseOffering, ?string $status = null): void
    {
        $status ??= EnrollmentStatus::ENROLLED->value;

        $student->courseOfferings()->attach($courseOffering->id, [
            'status' => $status,
            'enrolled_at' => $status === EnrollmentStatus::PROCESSING->value ? null : now()->toDateString(),
            'withdrawn_at' => null,
        ]);
    }

    protected function assignment(?CourseOffering $courseOffering = null, array $attributes = []): Assignment
    {
        $courseOffering ??= $this->courseOffering();

        return Assignment::create(array_merge([
            'course_offering_id' => $courseOffering->id,
            'title' => 'Assignment 1',
            'description' => 'Description',
            'due_date' => now()->addWeek()->toDateString(),
            'max_marks' => 100,
            'file_path' => 'assignments/fixture.pdf',
            'original_name' => 'fixture.pdf',
            'mime_type' => 'application/pdf',
            'status' => AssignmentStatus::SHOWN->value,
        ], $attributes));
    }

    protected function assignmentSubmission(?Assignment $assignment = null, ?Student $student = null, array $attributes = []): AssignmentSubmission
    {
        $assignment ??= $this->assignment();
        $student ??= $this->student();

        return AssignmentSubmission::create(array_merge([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'file_path' => 'assignment-submissions/fixture.pdf',
            'original_name' => 'fixture.pdf',
            'mime_type' => 'application/pdf',
            'comments' => 'My submission',
            'submitted_at' => now(),
            'status' => SubmissionStatus::ONTIME->value,
        ], $attributes));
    }

    protected function assignmentMark(?AssignmentSubmission $submission = null, ?Lecturer $lecturer = null, array $attributes = []): AssignmentMark
    {
        $submission ??= $this->assignmentSubmission();
        $lecturer ??= $this->lecturer();

        return AssignmentMark::create(array_merge([
            'assignment_submission_id' => $submission->id,
            'marks' => 80,
            'comments' => 'Good work',
            'marked_at' => now()->toDateString(),
            'lecturer_id' => $lecturer->id,
        ], $attributes));
    }

    protected function exam(?CourseOffering $courseOffering = null, array $attributes = []): Exam
    {
        $courseOffering ??= $this->courseOffering();

        return Exam::create(array_merge([
            'course_offering_id' => $courseOffering->id,
            'exam_type' => 'final',
            'exam_date' => now()->addWeek()->toDateString(),
            'max_marks' => 100,
            'weight' => 50,
        ], $attributes));
    }

    protected function examMark(?Exam $exam = null, ?Student $student = null, array $attributes = []): ExamMark
    {
        $exam ??= $this->exam();
        $student ??= $this->student();

        return ExamMark::create(array_merge([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'marks' => 70,
        ], $attributes));
    }

    protected function grade(?Student $student = null, ?CourseOffering $courseOffering = null, array $attributes = []): Grade
    {
        $courseOffering ??= $this->courseOffering();
        $student ??= $this->student();

        return Grade::create(array_merge([
            'student_id' => $student->id,
            'course_offering_id' => $courseOffering->id,
            'total_assignment_score' => 30,
            'total_test_marks' => 40,
        ], $attributes));
    }

    protected function attendance(?ClassSchedule $classSchedule = null, ?Student $student = null, array $attributes = []): Attendance
    {
        $classSchedule ??= $this->classSchedule();
        $student ??= $this->student();

        return Attendance::create(array_merge([
            'class_schedule_id' => $classSchedule->id,
            'student_id' => $student->id,
            'attendance_value' => 80,
            'recorded_at' => '08:15',
        ], $attributes));
    }

    protected function fakeUploadedFile(string $name = 'document.pdf'): UploadedFile
    {
        return UploadedFile::fake()->create($name, 100, 'application/pdf');
    }
}
