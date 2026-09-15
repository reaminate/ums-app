<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssignmentMarkResource;
use App\Models\AssignmentMark;
use App\Http\Requests\StoreAssignmentMarkRequest;
use App\Http\Requests\UpdateAssignmentMarkRequest;
use App\Notifications\AssignmentGraded;
use Illuminate\Http\Request;

class AssignmentMarkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', AssignmentMark::class)){
            abort(403);
        }
        $assignment_marks = AssignmentMark::query()
        ->when($request->has('lecturer'), function($query){
            $query->load('lecturer');
        })
        ->when($request->hasAny('assignment_submission'), function($query){
            $query->load('assignmentSubmission');
        })
        ->get();
        return AssignmentMarkResource::collection($assignment_marks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssignmentMarkRequest $request)
    {
        if($request->user()->cannot('create', AssignmentMark::class)){
            abort(403);
        }
        $assignment_mark = AssignmentMark::create($request->validated());
        $student = $assignment_mark->assignmentSubmission->student();
        $student->user->notify(new AssignmentGraded($assignment_mark));
        return response('', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AssignmentMark $assignment_mark, Request $request)
    {
        if($request->user()->cannot('view', $assignment_mark)){
            abort(403);
        }
        $assignment_mark->query()
        ->when($request->has('lecturer'), function($query){
            $query->load('lecturer');
        })
        ->when($request->hasAny('assignment_submission'), function($query){
            $query->load('assignmentSubmission');
        })
        ->get();
        return AssignmentMarkResource::make($assignment_mark);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssignmentMarkRequest $request, AssignmentMark $assignment_mark)
    {
        if($request->user()->cannot('update', $assignment_mark)){
            abort(403);
        }
        $assignment_mark->update($request->validated());
        return response('', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssignmentMark $assignment_mark, Request $request)
    {
        if($request->user()->cannot('delete', $assignment_mark)){
            abort(403);
        }
        $assignment_mark->delete();
        return response()->noContent();
    }
}
