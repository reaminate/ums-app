<?php

namespace App\Http\Controllers;

use App\Events\GradeUpdate;
use App\Http\Resources\ExamMarkResource;
use App\Models\ExamMark;
use App\Http\Requests\StoreExamMarkRequest;
use App\Http\Requests\UpdateExamMarkRequest;
use App\Models\Student;
use App\Notifications\ExamResultPublished;
use Illuminate\Http\Request;

class ExamMarkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', ExamMark::class)){
            abort(403);
        }
        $exam_mark = ExamMark::query()
        ->when($request->has('exam'), function($query){
            $query->load('exam');
        })
        ->when($request->has('student'), function($query){
            $query->load('student');
        })
        ->cursorPaginate(10);
        return ExamMarkResource::collection($exam_mark);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExamMarkRequest $request)
    {
        if($request->user()->cannot('create', ExamMark::class)){
            abort(403);
        }
        $exam_mark = ExamMark::create($request->validated());
        $student = Student::findOrFail($exam_mark->student_id);
        $student->user->notify(new ExamResultPublished($exam_mark));
        return response('', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ExamMark $exam_mark, Request $request)
    {
        if($request->user()->cannot('view', $exam_mark)){
            abort(403);
        }
        $exam_mark->query()
        ->when($request->has('exam'), function($query){
            $query->load('exam');
        })
        ->when($request->has('student'), function($query){
            $query->load('student');
        })
        ->get();
        return ExamMarkResource::make($exam_mark);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExamMarkRequest $request, ExamMark $exam_mark)
    {
        if($request->user()->cannot('update', $exam_mark)){
            abort(403);
        }
        $validated = $request->validated();
        $confirm = $validated['confirm']??false;
        unset($validated['confirm']);
        $exam_mark->update($request->validated());
        if($confirm){
            GradeUpdate::dispatch(0.0, $exam_mark);
        }
        return response('', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamMark $exam_mark, Request $request)
    {
        if($request->user()->cannot('delete', $exam_mark)){
            abort(403);
        }
        $exam_mark->delete();
        return response()->noContent();
    }
}
