<?php

use App\Http\Controllers\{ChapterController, CourseController, ImageCourseController, LessonController, MentorController, MyCourseController, ReviewController};
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'mentors'], function () {
    Route::get('/', [MentorController::class, 'index']);
    Route::get('{mentor}', [MentorController::class, 'show']);
    Route::post('/', [MentorController::class, 'store']);
    Route::put('{mentor}', [MentorController::class, 'update']);
    Route::delete('{mentor}', [MentorController::class, 'destroy']);
});

Route::group(['prefix' => 'courses'], function () {
    Route::get('/', [CourseController::class, 'index']);
    Route::get('{course}', [CourseController::class, 'show']);
    Route::post('/', [CourseController::class, 'store']);
    Route::put('{course}', [CourseController::class, 'update']);
    Route::delete('{course}', [CourseController::class, 'destroy']);
});

Route::group(['prefix' => 'chapters'], function () {
    Route::get('', [ChapterController::class, 'index']);
    Route::get('{chapter}', [ChapterController::class, 'show']);
    Route::post('/', [ChapterController::class, 'store']);
    Route::put('{chapter}', [ChapterController::class, 'update']);
    Route::delete('{chapter}', [ChapterController::class, 'destroy']);
});

Route::group(['prefix' => 'lessons'], function () {
    Route::get('', [LessonController::class, 'index']);
    Route::get('{lesson}', [LessonController::class, 'show']);
    Route::post('/', [LessonController::class, 'store']);
    Route::put('{lesson}', [LessonController::class, 'update']);
    Route::delete('{lesson}', [LessonController::class, 'destroy']);
});

Route::group(['prefix' => 'image-courses'], function () {
    Route::post('/', [ImageCourseController::class, 'store']);
    Route::put('{image}', [ImageCourseController::class, 'update']);
    Route::delete('{image}', [ImageCourseController::class, 'destroy']);
});

Route::group(['prefix' => 'my-courses'], function () {
    Route::get('/', [MyCourseController::class, 'index']);
    // Route::get('/{course}', [MyCourseController::class, 'registeredCourse']);
    Route::post('/', [MyCourseController::class, 'store']);
    Route::post('/premium', [MyCourseController::class, 'createPremiumAccess']);
    // Route::put('{myCourse}', [MyCourseController::class, 'update']);
    // Route::delete('{myCourse}', [MyCourseController::class, 'destroy']);
});

Route::group(['prefix' => 'reviews'], function () {
    Route::get('/{course}', [ReviewController::class, 'index']);
    Route::post('/', [ReviewController::class, 'store']);
    Route::put('{review}', [ReviewController::class, 'update']);
    Route::delete('{review}', [ReviewController::class, 'destroy']);
});
