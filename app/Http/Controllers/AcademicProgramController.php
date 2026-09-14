<?php

namespace App\Http\Controllers;

use App\Http\Resources\AcademicProgramResource;
use App\Models\AcademicProgram;
use App\Http\Requests\StoreAcademicProgramRequest;
use App\Http\Requests\UpdateAcademicProgramRequest;
use Illuminate\Http\Request;

class AcademicProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', AcademicProgram::class)){
            abort(403);
        }
        $academicProgram = AcademicProgram::query();
        $academicProgram->when($request->has('department'), function($query){
            $query->load('department');
        })
        ->when($request->has('courses'), function($query){
            $query->load('courses');
        })
        ->when($request->has('students'), function($query){
            $query->load('students');
        })->get();
        return AcademicProgramResource::collection($academicProgram);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAcademicProgramRequest $request)
    {
        if($request->user()->cannot('create', AcademicProgram::class)){
            abort(403);
        }
        AcademicProgram::create($request->validated());
        return response('', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicProgram $academic_program, Request $request)
    {
        if($request->user()->cannot('view', $academic_program)){
            abort(403);
        }
        $academic_program = AcademicProgram::query();
        $academic_program->when($request->has('department'), function($query){
            $query->load('department');
        })
        ->when($request->has('courses'), function($query){
            $query->load('courses');
        })
        ->when($request->has('students'), function($query){
            $query->load('students');
        })->get();
        return AcademicProgramResource::make($academic_program);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAcademicProgramRequest $request, AcademicProgram $academic_program)
    {
        if($request->user()->cannot('update', $academic_program)){
            abort(403);
        }
        $academic_program->update($request->validated());
        return response('', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicProgram $academic_program, Request $request)
    {
        if($request->user()->cannot('delete', $academic_program)){
            abort(403);
        }
        $academic_program->delete();
        return response()->noContent();
    }
}
