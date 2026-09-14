<?php

namespace App\Http\Controllers;

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
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        if($request->user()->cannot('create', Department::class)){
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department, Request $request)
    {
        if($request->user()->cannot('view', $department)){
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        if($request->user()->cannot('update', $department)){
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department, Request $request)
    {
        if($request->user()->cannot('delete', $department)){
            abort(403);
        }
    }
}
