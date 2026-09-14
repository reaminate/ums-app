<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;
use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
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
            $query->load('courseOffering');
        })
        ->when($request->has('assignment_submission'), function($query){
            $query->load('assignmentSubmissions');
        })
        ->get();
        return AssignmentResource::collection($assignment);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssignmentRequest $request)
    {
        if($request->user()->cannot('create', Assignment::class)){
            abort(403);
        }
        $validated = $request->validated();
        $file = $validated['file'];
        $stored_path = $file->store('assignments', 'public');

        Assignment::create([
            'course_offering_id' => $validated['course_offering_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_date' => $validated['due_date'],
            'max_marks' => $validated['max_marks'],
            'file_path' => $stored_path,
            'original_name' =>  $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'status' => $validated['status'],
        ]);
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
        $assignment->query()
        ->when($request->has('course_offering'), function($query){
            $query->load('courseOffering');
        })
        ->when($request->has('assignment_submission'), function($query){
            $query->load('assignmentSubmissions');
        })
        ->get();
        return AssignmentResource::make($assignment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssignmentRequest $request, Assignment $assignment)
    {
        if($request->user()->cannot('update', $assignment)){
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
        $assignment->update([$validated]);
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
