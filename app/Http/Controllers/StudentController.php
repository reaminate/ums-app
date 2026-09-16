<?php

namespace App\Http\Controllers;

use App\Enums\EnrollmentStatus;
use App\Enums\StudentStatus;
use App\Enums\UserType;
use App\Http\Requests\StudentEnrollRequest;
use App\Http\Resources\StudentResource;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Student;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\User;
use App\Notifications\EnrollmentStatusUpdate;
use App\Notifications\StudentCreated;
use Illuminate\Http\Request;
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
            $query->load('user');
        })
        ->when($request->has('academic_program'), function($query){
            $query->load('academicProgram');
        })
        ->when($request->has('course_offerings'), function($query){
            $query->load('courseOfferings');
        })
        ->when($request->has('assignment_submissions'), function($query){
            $query->load('assignmentSubmissions');
        })
        ->when($request->has('attendances'), function($query){
            $query->load('attendances');
        })
        ->when($request->has('exam_marks'), function($query){
            $query->load('examMarks');
        })
        ->when($request->has('grades'), function($query){
            $query->load('grades');
        })
        ->get();
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
    //     $validated = $request->validated();
    //     $user = User::findOrFail($validated['user_id']);
    //     if(isset($user)){
    //         $validated['name'] = $user->name;
    //         $validated['email'] = $user->email;
    //         $validated['enrollment_year'] = now()->year;
    //         $validated['status'] = StudentStatus::ENROLLED->value;
    //     }
    //     $courses = [];
    //     if(isset($validated['course_offerings'])){
    //         $courses = $validated['course_offerings'];
    //         unset($validated['course_offerings']);
    //     }
    //     $student = Student::create($validated);
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
        $student->query()
        ->when($request->has('user'), function($query){
            $query->load('user');
        })
        ->when($request->has('academic_program'), function($query){
            $query->load('academicProgram');
        })
        ->when($request->has('course_offerings'), function($query){
            $query->load('courseOfferings');
        })
        ->when($request->has('assignment_submissions'), function($query){
            $query->load('assignmentSubmissions');
        })
        ->when($request->has('attendances'), function($query){
            $query->load('attendances');
        })
        ->when($request->has('exam_marks'), function($query){
            $query->load('examMarks');
        })
        ->when($request->has('grades'), function($query){
            $query->load('grades');
        })
        ->get();
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
        $student->update($validated);
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
    public function enroll(Student $student, StudentEnrollRequest $request){
        if($request->user()->cannot('enroll', Student::class)){
            abort(403);
        }
        $validated = $request->validated();
        $courses_id = $validated['course_offering_id'];
        $pivotData = collect($validated)->only(['status', 'enrolled_at', 'withdrawn_at'])->all();

        foreach($courses_id as $course_id){
            $enrollment = $student->courseOfferings()->where('course_offering_id', $course_id)->first();
            if(!$enrollment){
                abort(404, "Student {$student->id} is not enrolled in course offering {$course_id}.");
            }

            if($pivotData['status'] === EnrollmentStatus::ENROLLED->value
                && $enrollment->enrolledStudents()->count() >= $enrollment->max_students){
                $enrollment->pivot->delete();
                abort(422, "Course offering {$course_id} has reached its maximum number of students. The pending enrollment request has been removed.");
            }

            $enrollment->pivot->update($pivotData);
            $student->user->notify(new EnrollmentStatusUpdate($student, $enrollment, $pivotData['status']));
        }

        return response('', 200);
    }
}
