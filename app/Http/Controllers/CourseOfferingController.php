<?php

namespace App\Http\Controllers;

use App\Models\CourseOffering;
use App\Http\Requests\StoreCourseOfferingRequest;
use App\Http\Requests\UpdateCourseOfferingRequest;
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
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseOfferingRequest $request)
    {
        if($request->user()->cannot('create', CourseOffering::class)){
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseOffering $course_offering, Request $request)
    {
        if($request->user()->cannot('view', $course_offering)){
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseOfferingRequest $request, CourseOffering $course_offering)
    {
        if($request->user()->cannot('update', $course_offering)){
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseOffering $course_offering, Request $request)
    {
        if($request->user()->cannot('delete', $course_offering)){
            abort(403);
        }
    }
}
