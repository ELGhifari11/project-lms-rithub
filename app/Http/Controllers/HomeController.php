<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function getCoursesByCategory(int $categoryId): JsonResponse
    {
        $courses = ClassModel::whereHas('subCategory.category', function ($query) use ($categoryId) {
            $query->where('id', $categoryId);
        })
            ->with(['mentor', 'subCategory', 'modules', 'modules.contents'])
            ->paginate(10);

        if ($courses->count() === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'No courses found for this category.'
            ], 404);
        }

        return response()->json($courses);
    }

    public function getNewestCourses(): JsonResponse
    {
        $courses = ClassModel::with(['mentor', 'subCategory', 'modules', 'modules.contents'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($courses->count() === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'No courses found.'
            ], 404);
        }

        return response()->json($courses);
    }

        public function getPopularCourses(): JsonResponse
    {
        $courses = ClassModel::with(['mentor', 'subCategory', 'modules', 'modules.contents'])
            ->withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->paginate(10);

        if ($courses->count() === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'No popular courses found.'
            ], 404);
        }

        return response()->json($courses);
    }

    public function getFilteredCourses(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'price' => 'nullable|in:free,paid',
            'duration' => 'nullable|string|regex:/^\d+\-\d+$/',
            'category' => 'nullable|integer|exists:categories,id',
            'search' => 'nullable|string|max:255',
        ]);

        $courses = ClassModel::with(['mentor', 'subCategory', 'modules', 'modules.contents'])
            ->when(isset($validated['price']), function ($query) use ($validated) {
                if ($validated['price'] === 'free') {
                    $query->where('price', 0);
                } elseif ($validated['price'] === 'paid') {
                    $query->where('price', '>', 0);
                }
            })
            ->when(isset($validated['duration']), function ($query) use ($validated) {
                if (str_contains($validated['duration'], '-')) {
                    [$min, $max] = explode('-', $validated['duration']);
                    $query->whereBetween('duration_minutes', [(int)$min, (int)$max]);
                } else {
                    $query->where('duration_minutes', '<=', (int)$validated['duration']);
                }
            })
            ->when(isset($validated['category']), function ($query) use ($validated) {
                $query->whereHas('subCategory.category', function ($q) use ($validated) {
                    $q->where('id', $validated['category']);
                });
            })
            ->when(isset($validated['search']), function ($query) use ($validated) {
                $query->where('title', 'like', '%' . $validated['search'] . '%')
                    ->orWhereHas('mentor', function ($q) use ($validated) {
                        $q->where('name', 'like', '%' . $validated['search'] . '%')
                            ->orWhere('username', 'like', '%' . $validated['search'] . '%');
                    });
            })
            ->paginate(10);

        return response()->json($courses);
    }
}
