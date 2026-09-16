<?php

namespace App\Jobs;

use App\Enums\AcademicStatus;
use App\Enums\SemesterStatus;
use App\Models\AcademicSemester;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SettingAcademicSemesterStatus implements ShouldQueue
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
        AcademicSemester::whereBeforeToday('registration_start_date')
        ->update(['status' => SemesterStatus::REGISTRATION_CLOSED]);

        AcademicSemester::where('registration_end_date', '<', now())
        ->where('start_date', '<', now())
        ->update(['status'=> SemesterStatus::REGISTRATION_OPEN]);

        AcademicSemester::where('start_date', '>', now())
        ->where('end_date', '<', now())
        ->update(['status'=> SemesterStatus::ONGOING]);

        AcademicSemester::whereAfterToday('end_date')
        ->update(['status'=> SemesterStatus::FINISHED]);

    }
}
