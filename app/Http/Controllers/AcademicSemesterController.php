<?php

namespace App\Http\Controllers;

use App\Http\Resources\AcademicSemesterResource;
use App\Models\AcademicSemester;
use App\Http\Requests\StoreAcademicSemesterRequest;
use App\Http\Requests\UpdateAcademicSemesterRequest;
use Illuminate\Http\Request;

class AcademicSemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', AcademicSemester::class)){
            abort(403);
        }
        $academic_semester = AcademicSemester::query()
        ->when($request->has('course_offerings'), function($query){
            $query->with('courseOfferings');
        })
        ->cursorPaginate(10);
        return AcademicSemesterResource::collection($academic_semester);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAcademicSemesterRequest $request)
    {
        if($request->user()->cannot('create', AcademicSemester::class)){
            abort(403);
        }
        AcademicSemester::create($request->validated());
        return response('', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicSemester $academic_semester, Request $request)
    {
        if($request->user()->cannot('view', $academic_semester)){
            abort(403);
        }
        $academic_semester->load(array_filter([
            $request->has('course_offerings') ? 'courseOfferings' : null,
        ]));
        return AcademicSemesterResource::make($academic_semester);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAcademicSemesterRequest $request, AcademicSemester $academic_semester)
    {
        if($request->user()->cannot('update', $academic_semester)){
            abort(403);
        }
        $academic_semester->update($request->validated());
        return response('', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicSemester $academic_semester, Request $request)
    {
        if($request->user()->cannot('delete', $academic_semester)){
            abort(403);
        }
        $academic_semester->delete();
        return response()->noContent();
    }
}
