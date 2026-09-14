<?php

namespace App\Http\Controllers;

use App\Models\Lecturer;
use App\Http\Requests\StoreLecturerRequest;
use App\Http\Requests\UpdateLecturerRequest;
use Illuminate\Http\Request;

class LecturerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', Lecturer::class)){
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLecturerRequest $request)
    {
        if($request->user()->cannot('create', Lecturer::class)){
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Lecturer $lecturer, Request $request)
    {
        if($request->user()->cannot('view', $lecturer)){
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLecturerRequest $request, Lecturer $lecturer)
    {
        if($request->user()->cannot('update', $lecturer)){
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lecturer $lecturer, Request $request)
    {
        if($request->user()->cannot('delete', $lecturer)){
            abort(403);
        }
    }
}
