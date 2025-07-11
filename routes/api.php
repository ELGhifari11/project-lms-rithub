<?php

use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\OptimationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/v1/profile', [ProfileController::class, 'profile']);
    Route::put('/v1/profile', [ProfileController::class, 'updateProfile']);

    Route::get('/v1/categories', [OptimationController::class, 'getCategories'])
        ->name('home.categories');
    Route::put('/v1/optimation', [OptimationController::class, 'optimation'])
        ->name('home.optimation');

    Route::get('/v1/courses/new', [HomeController::class, 'getNewestCourses'])
        ->name('home.courses.byNewest');
    Route::get('/v1/courses/popular', [HomeController::class, 'getPopularCourses'])
        ->name('home.courses.byPopular');
    Route::get('/v1/courses/filter/{format?}', [HomeController::class, 'getFilteredCourses'])
        ->name('home.courses.byFiltered');
    Route::get('/v1/courses/{categoryId}', [HomeController::class, 'getCoursesByCategory'])
        ->name('home.courses.byCategory');

    Route::get('/v1/courses/contents/{courseId}', [CourseController::class, 'getCourseContents'])
        ->name('course.contents');
    Route::post('/v1/courses/finished', [CourseController::class, 'finishedCourse'])
        ->name('course.finished');

    Route::get('/v1/mentors', [MentorController::class, 'getMentors']);
    Route::get('/v1/mentors/{mentorId}', [MentorController::class, 'getMentorById']);

    Route::post('/v1/bookmarks', [BookmarkController::class, 'addBookmark']);
    Route::get('/v1/bookmarks', [BookmarkController::class, 'getBookmarkeds']);
    Route::delete('/v1/bookmarks', [BookmarkController::class, 'delete']);
    Route::delete('/v1/bookmarks/bulk-delete', [BookmarkController::class, 'bulkDelete']);
    Route::delete('/v1/bookmarks/clear-all', [BookmarkController::class, 'clearAll']);

    Route::post('/v1/checkout', [CheckoutController::class, 'makeOrder'])
        ->name('checkout.make-order');
    Route::get('/v1/checkout/history', [CheckoutController::class, 'orderHistory'])
        ->name('checkout.history');
    Route::get('/v1/checkout/{order_id}', [CheckoutController::class, 'getOrder'])
        ->name('checkout.detail');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/webhook.php';
