<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Http\Requests\StoreGradeRequest;
use App\Http\Requests\UpdateGradeRequest;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', Grade::class)){
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGradeRequest $request)
    {
        if($request->user()->cannot('create', Grade::class)){
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Grade $grade, Request $request)
    {
        if($request->user()->cannot('view', $grade)){
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGradeRequest $request, Grade $grade)
    {
        if($request->user()->cannot('update', $grade)){
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grade $grade, Request $request)
    {
        if($request->user()->cannot('delete', $grade)){
            abort(403);
        }
    }
}
