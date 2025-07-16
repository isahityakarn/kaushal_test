<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    
    
    public function register(Request $request)
    {

   
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|lowercase|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;


        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Register',
        ]);

        // return $this->apiSuccess($user, 'save data', []);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|lowercase',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Login',
        ]);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'user' => "hello",

        ]);
        // return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function getProfile(Request $request)
    {


        $user = User::find($request->id);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Fatched Profile',
        ]);

        return response()->json([
            'user' => $user,
        ]);
    }


    public function updateProfile(Request $request)
    {
        // $user = $request->id();

        //  return response()->json([
        //     'user' => "sahitya",
        // ]);

        $user = User::find($request->id);
        $user->name = $request->name ? $request->name : $user->name;
        $user->email = $request->email ? $request->email : $user->email;
        $user->password = $request->email ?? Hash::make($request->password);
        $user->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Updated Profile',
        ]);

        return response()->json([
            'user' => $user,
            'message' => 'Profile updated'
        ]);
    }


    public function updateDelete(Request $request)
    {
        // $user = $request->id();

        //   

        $user = User::find($request->id);
        $user->delete();


        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Updated delete',
        ]);

        return response()->json([

            'message' => 'Profile Deleted'
        ]);
    }
}
