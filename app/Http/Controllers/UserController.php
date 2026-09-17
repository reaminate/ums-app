<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Events\NewUserCreated;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('viewAny', User::class)){
            abort(403);
        }
        $user = User::query()
        ->when($request->has('lecturer'), function($query){
            $query->load('lecturer');
        })
        ->when($request->has('student'), function($query){
            $query->load('student');
        })
        ->cursorPaginate(10);
        return UserResource::collection($user);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        if($request->user()->cannot('create', User::class)){
            abort(403);
        }
        $validated = $request->validated();
        $user = User::create($validated);
        $validated['id'] = $user->__get('id');
        NewUserCreated::dispatch($validated);
        return response('', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user, Request $request)
    {
        if($request->user()->cannot('view', $user)){
            abort(403);
        }
        $user->query()
        ->when($request->has('lecturer'), function($query){
            $query->load('lecturer');
        })
        ->when($request->has('student'), function($query){
            $query->load('student');
        })
        ->get();
        return UserResource::make($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if($request->user()->cannot('update', $user)){
            abort(403);
        }
        $user->update($request->validated());
        return response('', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, Request $request)
    {
        if($request->user()->cannot('delete', $user)){
            abort(403);
        }
        $user->delete();
        return response()->noContent();
    }
}
