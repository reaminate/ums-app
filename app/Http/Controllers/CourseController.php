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
            $query->with('department');
        })
        ->when($request->has('academic_programs'), function($query){
            $query->with('academicPrograms');
        })
        ->when($request->has('course_offerings'), function($query){
            $query->with('courseOfferings');
        })
        ->when($request->has('prerequisites'), function($query){
            $query->with('prerequisites');
        })
        ->when($request->has('prerequisite_for'), function($query){
            $query->with('prerequisiteFor');
        })
        ->cursorPaginate(10);
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
        $course_prerequisites = [];
        $course_prerequisites_for = [];
        $validated = $request->validated();
        if(isset($validated['course_prerequisite'])){
            $course_prerequisites = $validated['course_prerequisite'];
            unset($validated['course_prerequisite']);
        }
        if(isset($validated['course_prerequisite_for'])){
            $course_prerequisites_for = $validated['course_prerequisite_for'];
            unset($validated['course_prerequisite_for']);
        }
        $course = Course::make($validated);
        if(($course->course_level == 1 && $course_prerequisites)){
            return response([
                'error' => 'cannot have prerequisites for a course of this level',
            ],  401);
        }
        if(($course->course_level == 5 && $course_prerequisites_for)){
            return response([
                'error' => 'cannot be a prerequisites for another course this level'
            ],  401);
        }
        $course->save();
        $course->prerequisites()->sync($course_prerequisites);
        $course->prerequisiteFor()->sync($course_prerequisites_for);
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
        $course->load(array_filter([
            $request->has('department') ? 'department' : null,
            $request->has('academic_programs') ? 'academicPrograms' : null,
            $request->has('course_offerings') ? 'courseOfferings' : null,
            $request->has('prerequisites') ? 'prerequisites' : null,
            $request->has('prerequisite_for') ? 'prerequisiteFor' : null,
        ]));
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
        $course_prerequisites = [];
        $course_prerequisites_for = [];
        $validated = $request->validated();
        if(isset($validated['course_prerequisite'])){
            $course_prerequisites = $validated['course_prerequisite'];
            unset($validated['course_prerequisite']);
        }
        if(isset($validated['course_prerequisite_for'])){
            $course_prerequisites_for = $validated['course_prerequisite_for'];
            unset($validated['course_prerequisite_for']);
        }
        $new_course_level = $validated['course_level'] ?? $course->course_level;
        if(in_array($course->id, $course_prerequisites, true) || in_array($course->id, $course_prerequisites_for, true)){
            return response ([
                'error' => 'course cannot be a pre-requisite or have pre-requisite for self',
            ], 401);
        }
        if(($new_course_level == 1 && $course_prerequisites)){
            return response([
                'error' => 'cannot have prerequisites for a course of this level',
            ],  401);
        }
        if(($new_course_level == 5 && $course_prerequisites_for)){
            return response([
                'error' => 'cannot be a prerequisites for another course this level'
            ],  401);
        }
        $course->update($validated);
        $course->prerequisites()->sync($course_prerequisites);
        $course->prerequisiteFor()->sync($course_prerequisites_for);
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
