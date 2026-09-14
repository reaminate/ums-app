<?php

namespace App\Http\Controllers;

use App\Enums\StudentStatus;
use App\Enums\UserType;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\User;
use App\Notifications\StudentCreated;
use Illuminate\Http\Request;
use function Symfony\Component\Clock\now;

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
    public function store(StoreStudentRequest $request)
    {
        if($request->user()->cannot('create', Student::class)){
            abort(403);
        }
        $validated = $request->validated();
        $user = User::findOrFail($validated['user_id']);
        if(isset($user)){
            $validated['name'] = $user->name;
            $validated['email'] = $user->email;
            $validated['enrollment_year'] = now();
            $validated['status'] = StudentStatus::ENROLLED->value;
        }
        $student = Student::create($validated);
        $user->notify(new StudentCreated($user, $student));
        return response('', 201);
    }

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
        $student->update($request->validated());
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
}
