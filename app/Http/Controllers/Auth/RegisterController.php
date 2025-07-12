<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:users,email',
                'username' => 'required|string|unique:users,username',
                'password' => 'required|string|min:8',
                'password_confirmation' => 'required|string|same:password',
            ]);
            DB::beginTransaction();
            $user = User::create([
                'email' => $validated['email'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password'])
            ]);

            event(new Registered($user));

            $token = $user->createToken("register")->plainTextToken;
            DB::commit();

            return response()->json([
                'message' => 'Register berhasil, silahkan cek email untuk verifikasi',
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verificationNotice(): View
    {
        return view('auth.verify-email');
    }

    public function verify(Request $request, $userId): JsonResponse
    {
        try {
            if (!$request->hasValidSignature()) {
                return response()->json([
                    'message' => 'Invalid signature or expired verification code.'
                ], 400);
            }

            $user = User::findOrFail($userId);

            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 400);
            }

            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();

                return response()->json([
                    'message' => 'Email address successfully verified',
                    'user' => $user
                ], 200);
            }

            return response()->json([
                'message' => 'Email address already verified'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat verifikasi email.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resendEmailVerification(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Email verifikasi telah dikirim ulang'], 200);
    }

    public function verifiedEmail(Request $request): View
    {
        try {
            $id = $request->query('id');
            $hash = $request->query('hash');
            $expires = $request->query('expires');
            $signature = $request->query('signature');

            // pakai yg ini ketika sudah deploy di vercel
            // $response = Http::asForm()->post(config('app.url') . "/api/api/v1/email/verify/{$id}/{$hash}?expires={$expires}&signature={$signature}");
            $response = Http::asForm()->post(config('app.url') . "/api/v1/email/verify/{$id}/{$hash}?expires={$expires}&signature={$signature}");

            if ($response->successful()) {
                Log::info([$response->json()]);
                return view('verification.success');
            } else {
                Log::error('email verification failed', [$response->json()]);
                return view('verification.failed', [
                    'message' => 'Gagal verifikasi email. Silahkan coba lagi'
                ]);
            }
        } catch (\Exception $e) {
            Log::error("email verification failed", [$e->getMessage()]);
            return view('verification.failed', [
                'message' => 'terjadi kesalahan, silahkan coba lagi nanti.'
            ]);
        }
    }
}
