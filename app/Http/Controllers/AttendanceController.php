<?php

namespace App\Http\Controllers;

use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', Attendance::class)){
            abort(403);
        }
        $attendace = Attendance::query()
        ->when($request->has('student'), function($query){
            $query->load('student');
        })
        ->when($request->has('class_schedule'), function($query){
            $query->load('classSchedule');
        })
        ->cursorPaginate(10);
        return AttendanceResource::collection($attendace);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendanceRequest $request)
    {
        if($request->user()->cannot('create', Attendance::class)){
            abort(403);
        }
        Attendance::create($request->validated());
        return response('', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance, Request $request)
    {
        if($request->user()->cannot('view', $attendance)){
            abort(403);
        }
        $attendance->query()
        ->when($request->has('student'), function($query){
            $query->load('student');
        })
        ->when($request->has('class_schedule'), function($query){
            $query->load('classSchedule');
        })
        ->get();
        return AttendanceResource::make($attendance);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        if($request->user()->cannot('update', $attendance)){
            abort(403);
        }
        $attendance->update($request->validated());
        return response('', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance, Request $request)
    {
        if($request->user()->cannot('delete', $attendance)){
            abort(403);
        }
        $attendance->delete();
        return response()->noContent();
    }
}
