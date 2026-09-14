<?php

namespace App\Http\Controllers\api;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthUserLogin;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(StoreUserRequest $request)
    {
        $validated = $request->validated();
        if(!$request->user()->isAdmin() && $validated['type']===UserType::ADMIN->value){
            abort(403, 'you are unable to create admin users');
        }
        $user = User::create($validated);
        $token = $user->createToken('usertoken')->plainTextToken;
        return response([
            'message' => 'login successful',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
        ], 201);
    }
    public function login(AuthUserLogin $request)
    {
        if(!$request->validated()){
            return response()->json([
                'message'=> 'email or password is required'
            ], 422);
        }      
        $user = User::where("email", $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages([
                'error' => 'incorrect email or password'
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'login successful',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
        ], 200);   
    }
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'successfully logged out'
        ], 200);

    }
}
