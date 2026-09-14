<?php

namespace App\Http\Controllers;

use App\Http\Resources\LecturerResource;
use App\Models\Lecturer;
use App\Http\Requests\StoreLecturerRequest;
use App\Http\Requests\UpdateLecturerRequest;
use Illuminate\Http\Request;

class LecturerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', Lecturer::class)){
            abort(403);
        }
        $lecturer = Lecturer::query()
        ->when($request->has('user'), function($query){
            $query->load('user');
        })
        ->when($request->has('department'), function($query){
            $query->load('department');
        })
        ->when($request->has('course_offerings'), function($query){
            $query->load('courseOfferings');
        })
        ->when($request->has('assignment_marks'), function($query){
            $query->load('assignmentMarks');
        })
        ->get();
        return LecturerResource::collection($lecturer);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLecturerRequest $request)
    {
        if($request->user()->cannot('create', Lecturer::class)){
            abort(403);
        }
        Lecturer::create($request->validated());
        return response('', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lecturer $lecturer, Request $request)
    {
        if($request->user()->cannot('view', $lecturer)){
            abort(403);
        }
        $lecturer->query()
        ->when($request->has('user'), function($query){
            $query->load('user');
        })
        ->when($request->has('department'), function($query){
            $query->load('department');
        })
        ->when($request->has('course_offerings'), function($query){
            $query->load('courseOfferings');
        })
        ->when($request->has('assignment_marks'), function($query){
            $query->load('assignmentMarks');
        })
        ->get();
        return LecturerResource::make($lecturer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLecturerRequest $request, Lecturer $lecturer)
    {
        if($request->user()->cannot('update', $lecturer)){
            abort(403);
        }
        $lecturer->update($request->validated());
        return response('', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lecturer $lecturer, Request $request)
    {
        if($request->user()->cannot('delete', $lecturer)){
            abort(403);
        }
        $lecturer->delete();
        return response()->noContent();
    }
}
