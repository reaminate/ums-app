<?php

namespace App\Http\Controllers;

use App\Http\Resources\GradeResource;
use App\Models\Grade;
use App\Http\Requests\StoreGradeRequest;
use App\Http\Requests\UpdateGradeRequest;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', Grade::class)){
            abort(403);
        }
        $grade = Grade::query()
        ->when($request->has('student'), function($query){
            $query->with('student');
        })
        ->when($request->has('course_offering'), function($query){
            $query->with('courseOffering');
        })
        ->cursorPaginate(10);
        return GradeResource::collection($grade);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreGradeRequest $request)
    // {
    //     if($request->user()->cannot('create', Grade::class)){
    //         abort(403);
    //     }
    //     Grade::create($request->validated());
    //     return response('', 201);
    // }

    /**
     * Display the specified resource.
     */
    public function show(Grade $grade, Request $request)
    {
        if($request->user()->cannot('view', $grade)){
            abort(403);
        }
        $grade->load(array_filter([
            $request->has('student') ? 'student' : null,
            $request->has('course_offering') ? 'courseOffering' : null,
        ]));
        return GradeResource::make($grade);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGradeRequest $request, Grade $grade)
    {
        if($request->user()->cannot('update', $grade)){
            abort(403);
        }
        $grade->update($request->validated());
        return response('', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grade $grade, Request $request)
    {
        if($request->user()->cannot('delete', $grade)){
            abort(403);
        }
        $grade->delete();
        return response()->noContent();
    }
    /**
     * restore the model
     */
    public function restore(Grade $grade, Request $request)
    {
        if($request->user()->cannot('restore', $grade)){
            abort(403);
        }
        $grade->restore();
    }

    /**
     * permanently deletes a model
     */
    public function forceDelete(Grade $grade, Request $request)
    {
        if($request->user()->cannot('forceDelete', $grade)){
            abort(403);
        }
        $grade->forceDelete();
    }
}
