<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClassScheduleResource;
use App\Models\ClassSchedule;
use App\Http\Requests\StoreClassScheduleRequest;
use App\Http\Requests\UpdateClassScheduleRequest;
use App\Models\CourseOffering;
use App\Models\Lecturer;
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
        $class_schedules = ClassSchedule::all();
        $validated = $request->validated();
        if($this->conflictCheck($validated)){
            return response()->json([
                'message' => 'time slot is already booked'
            ], 422);
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
        $validated = $request->validated();
        $merge = array_keys(
            $class_schedule->only(['day', 'start_time', 'end_time', 'room_number', 'course_offering_id']),
            $validated
        );
        if($this->conflictCheck($merge, $class_schedule->id)){
            return response()->json([
                'message' => 'time slot is already booked'
            ], 422);
        }
        $class_schedule->update($validated);
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
    private function conflictCheck(array $validated, ?int $ignoreId = null):bool
    {
        $lecturer = CourseOffering::findOrFail($validated['course_offering_id'])->lecturer_id;

        return ClassSchedule::query()
        //finding the day
            ->where('day', $validated['day'])
            //finding time availability
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->where(function($query) use($validated, $lecturer){
                //finding the room and lecturer is already somewhere
                $query->where('room_number', $validated['room_number'])
                    ->orWhereHas('courseOffering', fn($q) => $q->where('lecturer_id', $lecturer));
            })
            ->when($ignoreId, fn($query) => $query->whereKeyNot($ignoreId))
            ->exists();
    }
}
