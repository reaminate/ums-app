<?php

namespace App\Http\Controllers;

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
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExamRequest $request)
    {
        if($request->user()->cannot('create', Exam::class)){
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam, Request $request)
    {
        if($request->user()->cannot('view', $exam)){
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExamRequest $request, Exam $exam)
    {
        if($request->user()->cannot('update', $exam)){
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam, Request $request)
    {
        if($request->user()->cannot('delete', $exam)){
            abort(403);
        }
    }
}
