<?php

namespace App\Jobs;

use App\Enums\CourseOfferingStatus;
use App\Models\CourseOffering;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ClosingCourseOfferingsWhereMaxHasReached implements ShouldQueue
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
        CourseOffering::where('status', CourseOfferingStatus::OPEN)->each(
            function($course_offering){
                $current = $course_offering->students()->count();
                if($current > $course_offering->max_students){
                    $course_offering->update(['status'=> CourseOfferingStatus::CLOSED->value]);
                }

            }
        );
    }
}
