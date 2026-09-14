<?php

namespace App\Http\Controllers;

use App\Models\ExamMark;
use App\Http\Requests\StoreExamMarkRequest;
use App\Http\Requests\UpdateExamMarkRequest;
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
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExamMarkRequest $request)
    {
        if($request->user()->cannot('create', ExamMark::class)){
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ExamMark $exam_mark, Request $request)
    {
        if($request->user()->cannot('view', $exam_mark)){
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExamMarkRequest $request, ExamMark $exam_mark)
    {
        if($request->user()->cannot('update', $exam_mark)){
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamMark $exam_mark, Request $request)
    {
        if($request->user()->cannot('delete', $exam_mark)){
            abort(403);
        }
    }
}
