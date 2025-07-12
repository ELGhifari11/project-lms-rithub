<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OptimationController extends Controller
{
    public function getCategories(): JsonResponse
    {
        $categories = Category::withCount('classes')->get();
        return response()->json($categories, 200);
    }

    public function optimation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'profession' => 'required|string',
            'preference' => 'required|exists:categories,id'
        ]);

        $user = $request->user();
        $user->update([
            'name' => $validated['name'],
            'profession' => $validated['profession'],
            'preference' => $validated['preference']
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User optimation completed',
        ], 200);
    }
}
