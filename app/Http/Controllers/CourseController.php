<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', Course::class)){
            abort(403);
        }
        $course = Course::query()
        ->when($request->has('department'), function($query){
            $query->load('department');
        })
        ->when($request->has('academic_programs'), function($query){
            $query->load('academicPrograms');
        })
        ->when($request->has('course_offerings'), function($query){
            $query->load('courseOfferings');
        })
        ->when($request->has('prerequisites'), function($query){
            $query->load('prerequisites');
        })
        ->when($request->has('prerequisite_for'), function($query){
            $query->load('prerequisiteFor');
        })
        ->get();
        return CourseResource::collection($course);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        if($request->user()->cannot('create', Course::class)){
            abort(403);
        }
        Course::create($request->validated());
        return response('', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course, Request $request)
    {
        if($request->user()->cannot('view', $course)){
            abort(403);
        }
        $course->query()
        ->when($request->has('department'), function($query){
            $query->load('department');
        })
        ->when($request->has('academic_programs'), function($query){
            $query->load('academicPrograms');
        })
        ->when($request->has('course_offerings'), function($query){
            $query->load('courseOfferings');
        })
        ->when($request->has('prerequisites'), function($query){
            $query->load('prerequisites');
        })
        ->when($request->has('prerequisite_for'), function($query){
            $query->load('prerequisiteFor');
        })
        ->get();
        return CourseResource::make($course);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        if($request->user()->cannot('update', $course)){
            abort(403);
        }
        $course->update($request->validated());
        return response('', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Request $request)
    {
        if($request->user()->cannot('delete', $course)){
            abort(403);
        }
        $course->delete();
        return response()->noContent();
    }
}
