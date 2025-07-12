<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'required|string',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($validated)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials.',
                ], 401);
            }

            $token = Auth::user()->createToken('login')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'User logged in successfully.',
                'token' => $token,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during login.',
            ], 500);
        }
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
