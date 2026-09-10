<?php

use App\Http\Controllers\AcademicProgramController;
use App\Http\Controllers\AcademicSemesterController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AssignmentMarkController;
use App\Http\Controllers\AssignmentSubmissionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClassScheduleController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseOfferingController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamMarkController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Route;

//not protected yet, testing out the controllers
Route::apiResource('/academic-program', AcademicProgramController::class);
Route::apiResource('/academic-semester', AcademicSemesterController::class);
Route::apiResource('/assignment', AssignmentController::class);
Route::apiResource('/assignment-mark', AssignmentMarkController::class);
Route::apiResource('/assignment-submission', AssignmentSubmissionController::class);
Route::apiResource('/attendance', AttendanceController::class);
Route::apiResource('/class-schedule', ClassScheduleController::class);
Route::apiResource('/course', CourseController::class);
Route::apiResource('/course-offering', CourseOfferingController::class);
Route::apiResource('/department', DepartmentController::class);
Route::apiResource('/exam', ExamController::class);
Route::apiResource('/exam-mark', ExamMarkController::class);
Route::apiResource('/faculty', FacultyController::class);
Route::apiResource('/grade', GradeController::class);
Route::apiResource('/lecturer', LecturerController::class);
Route::apiResource('/student', StudentController::class);
Route::apiResource('/user', UserController::class);

