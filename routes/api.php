<?php

use App\Http\Controllers\AcademicProgramController;
use App\Http\Controllers\AcademicSemesterController;
use App\Http\Controllers\api\AuthController;
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
Route::post('/login', [AuthController::class, 'login']);
//not protected yet, testing out the controllers
Route::middleware('auth:sanctum')->group(function(){
    Route::put('/student/{student}/enroll', [StudentController::class, 'enroll']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/course-offering/{id}/enroll');

    //force delete and restore
    Route::delete('/assignment-mark/{assignment_mark}/forcedelete', [AssignmentMarkController::class, 'forceDelete'])->withTrashed();
    Route::get('/assignment-mark/{assignment_mark}/restore', [AssignmentMarkController::class, 'restore'])->withTrashed();

    Route::delete('/assignment-submission/{assignment_submission}/forcedelete', [AssignmentSubmissionController::class, 'forceDelete'])->withTrashed();
    Route::get('/assignment-submission/{assignment_submission}/restore', [AssignmentSubmissionController::class, 'restore'])->withTrashed();

    Route::delete('/department/{department}/forcedelete', [DepartmentController::class, 'forceDelete'])->withTrashed();
    Route::get('/department/{department}/restore', [DepartmentController::class, 'restore'])->withTrashed();

    Route::delete('/exam-mark/{exam_mark}/forcedelete', [ExamMarkController::class, 'forceDelete'])->withTrashed();
    Route::get('/exam-mark/{exam_mark}/restore', [ExamMarkController::class, 'restore'])->withTrashed();

    Route::delete('/faculty/{faculty}/forcedelete', [FacultyController::class, 'forceDelete'])->withTrashed();
    Route::get('/faculty/{faculty}/restore', [FacultyController::class, 'restore'])->withTrashed();

    Route::delete('/grade/{grade}/forcedelete', [GradeController::class, 'forceDelete'])->withTrashed();
    Route::get('/grade/{grade}/restore', [GradeController::class, 'restore'])->withTrashed();

    Route::delete('/lecturer/{lecturer}/forcedelete', [LecturerController::class, 'forceDelete'])->withTrashed();
    Route::get('/lecturer/{lecturer}/restore', [LecturerController::class, 'restore'])->withTrashed();

    Route::delete('/student/{student}/forcedelete', [StudentController::class, 'forceDelete'])->withTrashed();
    Route::get('/student/{student}/restore', [StudentController::class, 'restore'])->withTrashed();

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

});
