<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Http\Requests\StoreFacultyRequest;
use App\Http\Requests\UpdateFacultyRequest;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', Faculty::class)){
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFacultyRequest $request)
    {
        if($request->user()->cannot('create', Faculty::class)){
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Faculty $faculty, Request $request)
    {
        if($request->user()->cannot('view', $faculty)){
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFacultyRequest $request, Faculty $faculty)
    {
        if($request->user()->cannot('update', $faculty)){
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faculty $faculty, Request $request)
    {
        if($request->user()->cannot('delete', $faculty)){
            abort(403);
        }
    }
}
