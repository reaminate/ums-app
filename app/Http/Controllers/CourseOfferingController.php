<?php

namespace App\Http\Controllers;

use App\Models\Course_offering;
use App\Http\Requests\StoreCourse_offeringRequest;
use App\Http\Requests\UpdateCourse_offeringRequest;

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
    public function store(StoreCourse_offeringRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Course_offering $course_offering)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourse_offeringRequest $request, Course_offering $course_offering)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course_offering $course_offering)
    {
        //
    }
}
