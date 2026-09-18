<?php

namespace App\Http\Controllers;

use App\Enums\LecturerStatus;
use App\Http\Resources\CourseOfferingResource;
use App\Models\CourseOffering;
use App\Http\Requests\StoreCourseOfferingRequest;
use App\Http\Requests\UpdateCourseOfferingRequest;
use App\Models\Lecturer;
use Illuminate\Http\Request;

class CourseOfferingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', CourseOffering::class)){
            abort(403);
        }
        $course_offering = CourseOffering::query()
        ->when($request->has('course'), function($query){
            $query->with('course');
        })
        ->when($request->has('semester'), function($query){
            $query->with('semester');
        })
        ->when($request->has('lecturer'), function($query){
            $query->with('lecturer');
        })
        ->when($request->has('students'), function($query){
            $query->with('students');
        })
        ->when($request->has('enrolled_students'), function($query){
            $query->with('enrolledStudents');
        })
        ->when($request->has('class_schedules'), function($query){
            $query->with('classSchedules');
        })
        ->when($request->has('assignments'), function($query){
            $query->with('assignments');
        })
        ->when($request->has('exams'), function($query){
            $query->with('exams');
        })
        ->when($request->has('grades'), function($query){
            $query->with('grades');
        })
        ->cursorPaginate(10);
        return CourseOfferingResource::collection($course_offering);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseOfferingRequest $request)
    {
        if($request->user()->cannot('create', CourseOffering::class)){
            abort(403);
        }

        $course_offering = CourseOffering::create($request->validated());
        Lecturer::findOrFail($course_offering->lecturer_id)->update(['status'=> LecturerStatus::TEACHING]);
        return response('', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseOffering $course_offering, Request $request)
    {
        if($request->user()->cannot('view', $course_offering)){
            abort(403);
        }
        $course_offering->load(array_filter([
            $request->has('course') ? 'course' : null,
            $request->has('semester') ? 'semester' : null,
            $request->has('lecturer') ? 'lecturer' : null,
            $request->has('students') ? 'students' : null,
            $request->has('enrolled_students') ? 'enrolledStudents' : null,
            $request->has('class_schedules') ? 'classSchedules' : null,
            $request->has('assignments') ? 'assignments' : null,
            $request->has('exams') ? 'exams' : null,
            $request->has('grades') ? 'grades' : null,
        ]));
        return CourseOfferingResource::make($course_offering);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseOfferingRequest $request, CourseOffering $course_offering)
    {
        if($request->user()->cannot('update', $course_offering)){
            abort(403);
        }
        $course_offering->update($request->validated());
        return response('', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseOffering $course_offering, Request $request)
    {
        if($request->user()->cannot('delete', $course_offering)){
            abort(403);
        }
        $course_offering->delete();
        return response()->noContent();
    }
}
