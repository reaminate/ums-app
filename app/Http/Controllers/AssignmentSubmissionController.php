<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssignmentSubmissionResource;
use App\Models\AssignmentSubmission;
use App\Http\Requests\StoreAssignmentSubmissionRequest;
use App\Http\Requests\UpdateAssignmentSubmissionRequest;
use Illuminate\Http\Request;

class AssignmentSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', AssignmentSubmission::class)){
            abort(403);
        }
        $assignment_submissions = AssignmentSubmission::query()
        ->when($request->has('assignment_mark'), function($query){
            $query->load('assignmentMark');
        })
        ->when($request->has('student'), function($query){
            $query->load('student');
        })
        ->when($request->has('assignment'), function($query){
            $query->load('assignment');
        })
        ->get();
        return AssignmentSubmissionResource::collection($assignment_submissions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssignmentSubmissionRequest $request)
    {
        if($request->user()->cannot('create', AssignmentSubmission::class)){
            abort(403);
        }
        AssignmentSubmission::create($request->validated());
        return response('', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AssignmentSubmission $assignment_submission, Request $request)
    {
        if($request->user()->cannot('view', $assignment_submission)){
            abort(403);
        }
        $assignment_submission->query()
        ->when($request->has('assignment_mark'), function($query){
            $query->load('assignmentMark');
        })
        ->when($request->has('student'), function($query){
            $query->load('student');
        })
        ->when($request->has('assignment'), function($query){
            $query->load('assignment');
        })
        ->get();

        return AssignmentSubmissionResource::make($assignment_submission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssignmentSubmissionRequest $request, AssignmentSubmission $assignment_submission)
    {
        if($request->user()->cannot('update', $assignment_submission)){
            abort(403);
        }
        $validated = $request->validated();
        if(isset($validated['file'])){
            $file = $validated['file'];
            $stored_path = $file->store('assignments', 'public');
            $validated['file_path'] = $stored_path;
            $validated['original_name'] = $file->getClientOriginalName();
            $validated['mime_type'] = $file->getMimeType();
            unset($validated['file']);
        }
        $assignment_submission->update($validated);
        return response('', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssignmentSubmission $assignment_submission, Request $request)
    {
        if($request->user()->cannot('delete', $assignment_submission)){
            abort(403);
        }
        $assignment_submission->delete();
        return response()->noContent();
    }
}
