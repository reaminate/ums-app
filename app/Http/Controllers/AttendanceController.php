<?php

namespace App\Http\Controllers;

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
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendanceRequest $request)
    {
        if($request->user()->cannot('create', Attendance::class)){
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance, Request $request)
    {
        if($request->user()->cannot('view', $attendance)){
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        if($request->user()->cannot('update', $attendance)){
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance, Request $request)
    {
        if($request->user()->cannot('delete', $attendance)){
            abort(403);
        }
    }
}
