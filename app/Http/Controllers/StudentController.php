<?php

namespace App\Http\Controllers;

use App\Enums\EnrollmentStatus;
use App\Enums\StudentStatus;
use App\Enums\UserType;
use App\Events\UserInfoUpdated;
use App\Http\Requests\StudentEnrollRequest;
use App\Http\Resources\StudentResource;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\User;
use App\Notifications\EnrollmentStatusUpdate;
use App\Notifications\StudentCreated;
use App\Services\EnrollmentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function PHPUnit\Framework\throwException;
;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', Student::class)){
            abort(403);
        }
        $student = Student::query()
        ->when($request->has('user'), function($query){
            $query->with('user');
        })
        ->when($request->has('academic_program'), function($query){
            $query->with('academicProgram');
        })
        ->when($request->has('course_offerings'), function($query){
            $query->with('courseOfferings');
        })
        ->when($request->has('assignment_submissions'), function($query){
            $query->with('assignmentSubmissions');
        })
        ->when($request->has('attendances'), function($query){
            $query->with('attendances');
        })
        ->when($request->has('exam_marks'), function($query){
            $query->with('examMarks');
        })
        ->when($request->has('grades'), function($query){
            $query->with('grades');
        })
        ->cursorPaginate(10);
        return StudentResource::collection($student);
    }

    /**
     * Store a newly created resource in storage.
     */
    //dont need this as student is automatically created by admin when creating user
    // public function store(StoreStudentRequest $request)
    // {
    //     if($request->user()->cannot('create', Student::class)){
    //         abort(403);
    //     }
    //     $student = $request->student();
    //     $user = User::findOrFail($student['user_id']);
    //     if(isset($user)){
    //         $student['name'] = $user->name;
    //         $student['email'] = $user->email;
    //         $student['enrollment_year'] = now()->year;
    //         $student['status'] = StudentStatus::ENROLLED->value;
    //     }
    //     $courses = [];
    //     if(isset($student['course_offerings'])){
    //         $courses = $student['course_offerings'];
    //         unset($student['course_offerings']);
    //     }
    //     $student = Student::create($student);
    //     $student->courseOfferings()->sync($courses);
    //     $user->notify(new StudentCreated($user, $student));
    //     return response('', 201);
    // }
    /**
     * Display the specified resource.
     */
    public function show(Student $student, Request $request)
    {
        if($request->user()->cannot('view', $student)){
            abort(403);
        }
        $student->load(array_filter([
            $request->has('user') ? 'user' : null,
            $request->has('academic_program') ? 'academicProgram' : null,
            $request->has('course_offerings') ? 'courseOfferings' : null,
            $request->has('assignment_submissions') ? 'assignmentSubmissions' : null,
            $request->has('attendances') ? 'attendances' : null,
            $request->has('exam_marks') ? 'examMarks' : null,
            $request->has('grades') ? 'grades' : null,
        ]));
        return StudentResource::make($student);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        if($request->user()->cannot('update', $student)){
            abort(403);
        }
        $validated = $request->validated();
        if(isset($validated['course_offerings'])){
            $courses = $validated['course_offerings'];
            unset($validated['course_offerings']);
            $student->courseOfferings()->sync($courses);
        }
        $changes = array_filter([
            'name' => $validated['name'] ?? null,
            'email' => $validated['email'] ?? null,
        ]);
        $student->update($validated);
        UserInfoUpdated::dispatch($student->user, $changes);
        return response('', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student, Request $request)
    {
        if($request->user()->cannot('delete', $student)){
            abort(403);
        }
        $student->delete();
        return response()->noContent();
    }

    /**
     * enrolls the students, only admins should be able to do this
     */
    public function enroll(Student $student, Request $request, EnrollmentService $enrollmentService){
        if($request->user()->cannot('enroll', Student::class)){
            abort(403);
        }
        $failed = $enrollmentService->enroll($student);
        if(!empty($failed)){
            return response()->json([
                'message' => 'failed_some',
                'failed' => $failed,
            ], 200);
        }
        return response()->json([
            'message' => 'success'
        ], 200);
    }
    /**
     * restore the model
     */
    public function restore(Student $student, Request $request)
    {
        if($request->user()->cannot('restore', $student)){
            abort(403);
        }
        $student->restore();
    }

    /**
     * permanently deletes a model
     */
    public function forceDelete(Student $student, Request $request)
    {
        if($request->user()->cannot('forceDelete', $student)){
            abort(403);
        }
        $student->forceDelete();
    }
}
