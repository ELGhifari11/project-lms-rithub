<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!auth()->attempt($validated)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $token = Auth::user()->createToken('login token for '. Auth::user()->email)->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'User logged in successfully.',
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user()->currentAccessToken()->delete();

        if (!$user) return response()->json(['status' => 'error', 'message' => 'Something went wrong'], 400);

        return response()->json([
            'status' => 'success',
            'message' => 'User logged out successfully.'
        ], 200);
    }
}
