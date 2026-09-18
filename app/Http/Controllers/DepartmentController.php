<?php

namespace App\Http\Controllers;

use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', Department::class)){
            abort(403);
        }
        $department = Department::query()
        ->when($request->has('faculty'), function($query){
            $query->with('faculty');
        })
        ->when($request->has('academic_programs'), function($query){
            $query->with('academicPrograms');
        })
        ->when($request->has('courses'), function($query){
            $query->with('courses');
        })
        ->when($request->has('lecturers'), function($query){
            $query->with('lecturers');
        })
        ->cursorPaginate(10);
        return DepartmentResource::collection($department);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        if($request->user()->cannot('create', Department::class)){
            abort(403);
        }
        Department::create($request->validated());
        return response('', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department, Request $request)
    {
        if($request->user()->cannot('view', $department)){
            abort(403);
        }
        $department->load(array_filter([
            $request->has('faculty') ? 'faculty' : null,
            $request->has('academic_programs') ? 'academicPrograms' : null,
            $request->has('courses') ? 'courses' : null,
            $request->has('lecturers') ? 'lecturers' : null,
        ]));
        return DepartmentResource::make($department);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        if($request->user()->cannot('update', $department)){
            abort(403);
        }
        $department->update($request->validated());
        return response('', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department, Request $request)
    {
        if($request->user()->cannot('delete', $department)){
            abort(403);
        }
        $department->delete();
        return response()->noContent();
    }
}
