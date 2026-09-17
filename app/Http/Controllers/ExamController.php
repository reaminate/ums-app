<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExamResource;
use App\Models\Exam;
use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', Exam::class)){
            abort(403);
        }
        $exam = Exam::query()
        ->when($request->has('course_offering'), function($query){
            $query->load('courseOffering');
        })
        ->when($request->has('exam_marks'), function($query){
            $query->load('examMarks');
        })
        ->cursorPaginate(10);
        return ExamResource::collection($exam);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExamRequest $request)
    {
        if($request->user()->cannot('create', Exam::class)){
            abort(403);
        }
        Exam::create($request->validated());
        return response('', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam, Request $request)
    {
        if($request->user()->cannot('view', $exam)){
            abort(403);
        }
        $exam->query()
        ->when($request->has('course_offering'), function($query){
            $query->load('courseOffering');
        })
        ->when($request->has('exam_marks'), function($query){
            $query->load('examMarks');
        })
        ->get();
        return ExamResource::make($exam);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExamRequest $request, Exam $exam)
    {
        if($request->user()->cannot('update', $exam)){
            abort(403);
        }
        $exam->update($request->validated());
        return response('', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam, Request $request)
    {
        if($request->user()->cannot('delete', $exam)){
            abort(403);
        }
        $exam->delete();
        return response()->noContent();
    }
}
