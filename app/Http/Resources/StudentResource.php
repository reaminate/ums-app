<?php

namespace App\Http\Resources;

use App\Enums\AttendanceStatus;
use App\Enums\SemesterStatus;
use App\Models\AcademicSemester;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_more_info' => $this->when(
                $request->user()?->isAdmin(),
                fn()=> UserResource::make($this->whenLoaded('user')),
            ),
            'student_number' => $this->student_number,
            'name' => $this->name,
            'email' => $this->email,
            'program_id' => $this->program_id,
            'program_more_info' => AcademicProgramResource::make($this->whenLoaded('academicProgram')),
            'enrollment_year'=>$this->enrollment_year,
            'status' => $this->status,
            'assignment_submissions' => AssignmentSubmissionResource::collection($this->whenLoaded('assignmentSubmissions')),
            'attendance_per_class' => $this->whenLoaded('attendances', function () use ($request) {
                $attendances = $this->attendances;

                if (! $request->user()?->isAdmin()) {
                    $ongoingSemesterId = static::ongoingSemesterId();

                    $attendances = $attendances->filter(
                        fn ($attendance) => $attendance->classSchedule->courseOffering->semester_id === $ongoingSemesterId
                    );
                }

                return $attendances
                    ->groupBy(fn ($attendance) => $attendance->classSchedule->course_offering_id)
                    ->map(function ($attendances) {
                        $courseOffering = $attendances->first()->classSchedule->courseOffering;
                        $totalSessions = $attendances->count();
                        $attendanceScore = $attendances->sum(fn ($attendance) => $attendance->status->weight());

                        return [
                            'course_name' => $courseOffering->course->name,
                            'present' => $attendances->where('status', AttendanceStatus::PRESENT)->count(),
                            'absent' => $attendances->where('status', AttendanceStatus::ABSENT)->count(),
                            'late' => $attendances->where('status', AttendanceStatus::LATE)->count(),
                            'excused' => $attendances->where('status', AttendanceStatus::EXCUSED)->count(),
                            'attendance_percentage' => $totalSessions > 0
                                ? round($attendanceScore / $totalSessions * 100, 1)
                                : 0.0,
                        ];
                    })
                    ->values();
            }),
        ];
    }

    /**
     * The id of the currently ongoing academic semester, memoized for the
     * duration of the request so a resource collection only queries it once.
     */
    protected static function ongoingSemesterId(): ?int
    {
        return once(fn () => AcademicSemester::where('status', SemesterStatus::ONGOING)->value('id'));
    }
}
