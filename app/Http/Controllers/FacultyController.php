<?php

namespace App\Http\Controllers;

use App\Http\Resources\FacultyResource;
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
        $faculty = Faculty::query()
        ->when($request->has('departments'), function($query){
            $query->with('departments');
        })
        ->cursorPaginate(10);
        return FacultyResource::collection($faculty);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFacultyRequest $request)
    {
        if($request->user()->cannot('create', Faculty::class)){
            abort(403);
        }
        Faculty::create($request->validated());
        return response('', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Faculty $faculty, Request $request)
    {
        if($request->user()->cannot('view', $faculty)){
            abort(403);
        }
        $faculty->load(array_filter([
            $request->has('departments') ? 'departments' : null,
        ]));
        return FacultyResource::make($faculty);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFacultyRequest $request, Faculty $faculty)
    {
        if($request->user()->cannot('update', $faculty)){
            abort(403);
        }
        $faculty->update($request->validated());
        return response('', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faculty $faculty, Request $request)
    {
        if($request->user()->cannot('delete', $faculty)){
            abort(403);
        }
        $faculty->delete();
        return response()->noContent();
    }
}
