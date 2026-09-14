<?php

namespace App\Http\Controllers;

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
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssignmentSubmissionRequest $request)
    {
        if($request->user()->cannot('create', AssignmentSubmission::class)){
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AssignmentSubmission $assignment_submission, Request $request)
    {
        if($request->user()->cannot('view', $assignment_submission)){
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssignmentSubmissionRequest $request, AssignmentSubmission $assignment_submission)
    {
        if($request->user()->cannot('update', $assignment_submission)){
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssignmentSubmission $assignment_submission, Request $request)
    {
        if($request->user()->cannot('delete', $assignment_submission)){
            abort(403);
        }
    }
}
