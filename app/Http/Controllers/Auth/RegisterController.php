<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpEmail;
use App\Models\OTP;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|same:password',
        ]);

        $user = User::create([
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password'])
        ]);

        $code = rand(100000, 999999);
        $verification = OTP::create([
            'user_id' => $user->id,
            'unique_id' => uniqid(),
            'otp' => Hash::make($code),
            'type' => 'register',
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new OtpEmail($code));

        $token = $user->createToken('register token for' . $user->email)->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully. Please verify your email.',
            'unique_id' => $verification->unique_id,
            'token' => $token,
        ], 201);
    }

    public function verify(Request $request, $uniqueId): JsonResponse
    {
        $verify = OTP::whereUserId($request->user()->id)
            ->whereUniqueId($uniqueId)
            ->whereType('register')
            ->where('expires_at', '>', now())
            ->first();

        if (!$verify) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired verification code.'
            ], 400);
        }

        if (!Hash::check($request->input('otp'), $verify->otp)) {
            $verify->status = 'invalid';
            $verify->save();
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP code.'
            ], 400);
        }

        $user = User::findOrFail($verify->user_id);
        $user->email_verified_at = now();
        $user->save();

        if (!$user) return response()->json([
            'status' => 'error',
            'message' => 'User not found.'
        ], 404);
        $verify->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Email verified successfully.',
        ], 200);
    }

    public function resendOtp(Request $request, $uniqueId): JsonResponse
    {
        $code = rand(100000, 999999);
        OTP::where('unique_id', $uniqueId)->update([
            'otp' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
            'status' => 'active',
        ]);

        Mail::to($request->user()->email)->send(new OtpEmail($code));

        return response()->json([
            'status' => 'success',
            'message' => 'OTP resent successfully.',
        ], 200);
    }
}
