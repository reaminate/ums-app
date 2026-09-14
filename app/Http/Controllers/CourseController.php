<?php

namespace App\Http\Controllers;

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
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        if($request->user()->cannot('create', Course::class)){
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course, Request $request)
    {
        if($request->user()->cannot('view', $course)){
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        if($request->user()->cannot('update', $course)){
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Request $request)
    {
        if($request->user()->cannot('delete', $course)){
            abort(403);
        }
    }
}
