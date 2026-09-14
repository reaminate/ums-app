<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use Illuminate\Http\Request;

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
        return StudentResource::collection(Student::all()->load('attendances.classSchedule.courseOffering.course'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request)
    {
        if($request->user()->cannot('create', Student::class)){
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student, Request $request)
    {
        if($request->user()->cannot('view', $student)){
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        if($request->user()->cannot('update', $student)){
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student, Request $request)
    {
        if($request->user()->cannot('delete', $student)){
            abort(403);
        }
    }
}
