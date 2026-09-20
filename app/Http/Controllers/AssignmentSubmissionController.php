<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Http\Resources\AssignmentSubmissionResource;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Http\Requests\StoreAssignmentSubmissionRequest;
use App\Http\Requests\UpdateAssignmentSubmissionRequest;
use App\Services\AssignmentSubmissionService;
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
            $query->with('assignmentMark');
        })
        ->when($request->has('student'), function($query){
            $query->with('student');
        })
        ->when($request->has('assignment'), function($query){
            $query->with('assignment');
        })
        ->cursorPaginate(10);
        return AssignmentSubmissionResource::collection($assignment_submissions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssignmentSubmissionRequest $request, AssignmentSubmissionService $assignment_submission_service)
    {
        if($request->user()->cannot('create', AssignmentSubmission::class)){
            abort(403);
        }
        $validated = $request->validated();
        $assignment_submission_service->create($validated, $request->user()->student);
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
        $assignment_submission->load(array_filter([
            $request->has('assignment_mark') ? 'assignmentMark' : null,
            $request->has('student') ? 'student' : null,
            $request->has('assignment') ? 'assignment' : null,
        ]));

        return AssignmentSubmissionResource::make($assignment_submission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssignmentSubmissionRequest $request, AssignmentSubmission $assignment_submission, AssignmentSubmissionService $assignment_submission_service)
    {
        if($request->user()->cannot('update', $assignment_submission)){
            abort(403);
        }
        $validated = $request->validated();
        $assignment_submission_service->update($validated, $assignment_submission, $request->user()->student);
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
    /**
     * restore the model
     */
    public function restore(AssignmentSubmission $assignment_submission, Request $request)
    {
        if($request->user()->cannot('restore', $assignment_submission)){
            abort(403);
        }
        $assignment_submission->restore();
    }

    /**
     * permanently deletes a model
     */
    public function forceDelete(AssignmentSubmission $assignment_submission, Request $request)
    {
        if($request->user()->cannot('forceDelete', $assignment_submission)){
            abort(403);
        }
        $assignment_submission->forceDelete();
    }
}
