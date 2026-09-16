<?php

namespace App\Jobs;

use App\Enums\CourseOfferingStatus;
use App\Models\CourseOffering;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SettingStatusOfCourseOfferings implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;
    /**
     * Create a new job instance.
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        CourseOffering::where('start_date', '>', now())
            ->update(['status' => CourseOfferingStatus::OPEN]);

        CourseOffering::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->update(['status' => CourseOfferingStatus::ONGOING]);

        CourseOffering::where('end_date', '<', now())
            ->update(['status' => CourseOfferingStatus::CLOSED]);
    }
}
