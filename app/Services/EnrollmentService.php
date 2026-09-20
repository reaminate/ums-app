<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Events\CreateGrade;
use App\Models\Enrollment;
use App\Models\Student;
use App\Notifications\EnrollmentStatusUpdate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EnrollmentService
{
    /**
     * Enroll a student into every course offering they are pending (not yet enrolled) in.
     *
     */
    public function enroll(Student $student): array
    {
        $student_courses = Enrollment::where('student_id', $student->id)->where('enrolled_at', null)->get();
        $courses_id = $student_courses->pluck('course_offering_id');
        $failed = [];

        foreach ($courses_id as $course_id) {
            DB::beginTransaction();
            try {
                $enrollment = $student->courseOfferings()->where('course_offering_id', $course_id)->lockForUpdate()->first();
                if (!$enrollment) {
                    abort(404, "Student {$student->id} is not enrolled in course offering {$course_id}.");
                }

                if ($enrollment->enrolledStudents()->lockForUpdate()->count() >= $enrollment->max_students) {
                    $student->user->notify(new EnrollmentStatusUpdate($student, $enrollment, EnrollmentStatus::FAILED->name));
                    throw (new ModelNotFoundException())->setModel(Enrollment::class, [$course_id]);
                }
                $enrollment->pivot->update([
                    'status' => EnrollmentStatus::ENROLLED->value,
                    'enrolled_at' => now(),
                ]);
                CreateGrade::dispatch($student, $enrollment);
                DB::commit();
                $student->user->notify(new EnrollmentStatusUpdate($student, $enrollment, EnrollmentStatus::ENROLLED->name));
            } catch (ModelNotFoundException $e) {
                DB::rollBack();
                report($e);
                $failed[] = ['course_offering_id' => $course_id, 'reason' => 'max student enrollment has reached for an offering'];
                continue;
            } catch (NotFoundHttpException $e) {
                DB::rollBack();
                report($e);
                $failed[] = ['course_offering_id' => $course_id, 'reason' => 'student not enrolled in this course'];
                continue;
            }
        }

        return $failed;
    }
}
