<?php

namespace App\Http\Controllers;

use App\Models\CourseOffering;
use App\Http\Requests\StoreCourseOfferingRequest;
use App\Http\Requests\UpdateCourseOfferingRequest;

class CourseOfferingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseOfferingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseOffering $course_offering)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseOfferingRequest $request, CourseOffering $course_offering)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseOffering $course_offering)
    {
        //
    }
}
