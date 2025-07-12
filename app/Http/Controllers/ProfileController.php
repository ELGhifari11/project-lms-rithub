<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile(Request $request): JsonResponse
    {
        $user = User::with('enrollments')->findOrFail($request->user()->id);

        return response()->json($user, 200);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'avatar_url' => 'nullable',
            'name' => 'nullable|string|max:255',
            'profession' => 'nullable|string|max:255',
            'preference' => 'nullable|integer|exists:categories,id'
        ]);

        $user = $request->user();

        // if (isset($validated['profile_picture'])) {
        //     $user->profile_picture = $validated['profile_picture'];
        // }

        // if (isset($validated['name'])) {
        //     $user->name = $validated['name'];
        // }

        // if (isset($validated['profession'])) {
        //     $user->profession = $validated['profession'];
        // }

        // if (isset($validated['preference'])) {
        //     $user->preference = $validated['preference'];
        // }
        // $user->save();
        $user->fill($validated)->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }
}
