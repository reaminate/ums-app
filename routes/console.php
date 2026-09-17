<?php

use App\Jobs\ClosingCourseOfferingsWhereMaxHasReached;
use App\Jobs\SettingAcademicSemesterStatus;
use App\Jobs\SettingStatusOfCourseOfferings;
use App\Jobs\SettingStudentsFinalsViability;
use App\Models\ClassSchedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

$earliest_class_end_time = ClassSchedule::orderBy('end_time', 'asc')->first(['end_time']);
$lastest_class_end_time = ClassSchedule::orderBy('end_time', 'desc')->first(['end_time']);
//update course offering status daily
Schedule::job(new SettingStatusOfCourseOfferings())->dailyAt('06:00');
//closes the course offerings when max student capacity has reached
Schedule::job(new ClosingCourseOfferingsWhereMaxHasReached())->twiceMonthly(1,15, '01:00');
//updates academic semester status monthly
Schedule::job(new SettingAcademicSemesterStatus())->monthly();
//updates the status of attendance
Schedule::job(new SettingStudentsFinalsViability())->between($earliest_class_end_time->end_time, $lastest_class_end_time->end_time)->hourly();
