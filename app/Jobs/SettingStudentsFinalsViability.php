<?php

namespace App\Jobs;

use App\Enums\AttendanceStatus;
use App\Enums\CourseOfferingStatus;
use App\Enums\StudentStatus;
use App\Models\Attendance;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SettingStudentsFinalsViability implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Attendance::whereHas('classSchedule.courseOffering', function ($query) {
            $query->where('status', CourseOfferingStatus::ONGOING);
        })
        ->whereHas('student', function ($query) {
            $query->where('status', StudentStatus::ENROLLED);
        })
        ->each(
            function ($attendance) {
                $viability = ($attendance->attendance_value) / ($attendance->total_classes);
                $attendance->status = match (true) {
                    $viability >= 0.6 => AttendanceStatus::VIABLE->value,
                    $viability >= 0.5 => AttendanceStatus::BARELY_VIABLE->value,
                    default => AttendanceStatus::NOTVIABLE->value,
                };
                $attendance->save();
            }
        );
    }
}