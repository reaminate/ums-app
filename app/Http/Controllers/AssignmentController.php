<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;
use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use App\Models\CourseOffering;
use App\Models\Student;
use App\Notifications\NewAssignmentPublished;
use App\Services\AssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', Assignment::class)){
            abort(403);
        }
        $assignment = Assignment::query()
        ->when($request->has('course_offering'), function($query){
            $query->with('courseOffering');
        })
        ->when($request->has('assignment_submission'), function($query){
            $query->with('assignmentSubmissions');
        })
        ->cursorPaginate(10);
        return AssignmentResource::collection($assignment);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssignmentRequest $request, AssignmentService $assignment_service)
    {
        if($request->user()->cannot('create', Assignment::class)){
            abort(403);
        }
        $validated = $request->validated();
        $assignment_service->store($validated);
        return response('', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment, Request $request)
    {
        if($request->user()->cannot('view', $assignment)){
            abort(403);
        }
        $assignment->load(array_filter([
            $request->has('course_offering') ? 'courseOffering' : null,
            $request->has('assignment_submission') ? 'assignmentSubmissions' : null,
        ]));
        return AssignmentResource::make($assignment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssignmentRequest $request, Assignment $assignment, AssignmentService $assignment_service)
    {
        if($request->user()->cannot('update', $assignment)){
            abort(403);
        }
        $validated = $request->validated();
        $assignment_service->update($validated, $assignment);
        return response('', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment, Request $request)
    {
        if($request->user()->cannot('delete', $assignment)){
            abort(403);
        }
        $assignment->delete();
        return response()->noContent();
    }
}
