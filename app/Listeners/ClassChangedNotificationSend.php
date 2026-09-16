<?php

namespace App\Listeners;

use App\Enums\CourseOfferingStatus;
use App\Events\ClassChanged;
use App\Notifications\ClassHasChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ClassChangedNotificationSend
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ClassChanged $event): void
    {
        $class_schedule = $event->class_schedule;
        $course_offering = $class_schedule->courseOffering;

        if ($course_offering->status !== CourseOfferingStatus::ONGOING) {
            return;
        }

        $course_offering->students()
            ->with('user')
            ->get()
            ->each(fn ($student) => $student->user->notify(new ClassHasChanged($class_schedule)));
    }
}
