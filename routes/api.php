<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\EssayReviewController;
use App\Http\Controllers\DashboardController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/tests/test-answer', [TestController::class, 'userSolution']);

    // 🛡️ Chỉ dành cho Admin
    Route::middleware('role:Admin')->group(function () {
        Route::post('/tests/generate', [TestController::class, 'generateTest']);
        Route::get('/subjects', [TestController::class, 'getSubjects']);
        Route::get('/question-types', [TestController::class, 'getQuestionTypes']);
        Route::delete('/tests/{id}', [TestController::class, 'destroy']);
        Route::post('/essay-reviews', [EssayReviewController::class, 'store']);
        Route::get('/admin-only', fn () => response()->json(['message' => 'Welcome admin!']));
        Route::apiResource('users', UserController::class)->except(['show']);
    });

    // 🛡️ Chỉ dành cho Student
    Route::middleware('role:Student')->group(function () {
        Route::post('/dashboard', [DashboardController::class, 'store']);
        Route::get('/student-only', fn () => response()->json(['message' => 'Welcome student!']));
    });

    // 🛡️ Dùng chung cho Admin và Student
    Route::middleware('role:Admin|Student')->group(function () {
        Route::post('/tests/storeUserAnswers', [TestController::class, 'storeUserAnswers']);
        Route::get('/tests', [TestController::class, 'index']);
        Route::get('/tests/{id}', [TestController::class, 'show']);
        Route::get('/tests/result', [TestController::class, 'testResult']);
        Route::post('/essay-reviews', [EssayReviewController::class, 'store']);
        Route::get('/statistics', [StatisticsController::class, 'store']);
    });
});
