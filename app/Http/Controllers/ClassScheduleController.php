<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Http\Requests\StoreClassScheduleRequest;
use App\Http\Requests\UpdateClassScheduleRequest;
use Illuminate\Http\Request;

class ClassScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', ClassSchedule::class)){
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassScheduleRequest $request)
    {
        if($request->user()->cannot('create', ClassSchedule::class)){
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassSchedule $class_schedule, Request $request)
    {
        if($request->user()->cannot('view', $class_schedule)){
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassScheduleRequest $request, ClassSchedule $class_schedule)
    {
        if($request->user()->cannot('update', $class_schedule)){
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassSchedule $class_schedule, Request $request)
    {
        if($request->user()->cannot('delete', $class_schedule)){
            abort(403);
        }
    }
}
