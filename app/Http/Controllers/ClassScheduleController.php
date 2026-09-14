<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClassScheduleResource;
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
        $class_schedule = ClassSchedule::query()
        ->when($request->has('course_offering'), function($query){
            $query->load('courseOffering');
        })
        ->when($request->has('attendances'), function($query){
            $query->load('attendances');
        })
        ->get();
        return ClassScheduleResource::collection($class_schedule);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassScheduleRequest $request)
    {
        if($request->user()->cannot('create', ClassSchedule::class)){
            abort(403);
        }
        ClassSchedule::create($request->validated());
        return response('', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassSchedule $class_schedule, Request $request)
    {
        if($request->user()->cannot('view', $class_schedule)){
            abort(403);
        }
        $class_schedule->query()
        ->when($request->has('course_offering'), function($query){
            $query->load('courseOffering');
        })
        ->when($request->has('attendances'), function($query){
            $query->load('attendances');
        })
        ->get();
        return ClassScheduleResource::make($class_schedule);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassScheduleRequest $request, ClassSchedule $class_schedule)
    {
        if($request->user()->cannot('update', $class_schedule)){
            abort(403);
        }
        $class_schedule->update($request->validated());
        return response('', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassSchedule $class_schedule, Request $request)
    {
        if($request->user()->cannot('delete', $class_schedule)){
            abort(403);
        }
        $class_schedule->delete();
        return response()->noContent();
    }
}
