<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\EssayReviewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\QuestionController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/tests/test-answer', [TestController::class, 'userSolution']);
    Route::get('/dashboard', [DashboardController::class, 'stats']);

    // 🛡️ Chỉ dành cho Admin
    Route::middleware('role:Admin')->group(function () {
        Route::post('/tests/generate', [TestController::class, 'generateTest']);
        Route::get('/subjects', [TestController::class, 'getSubjects']);
        Route::get('/question-types', [TestController::class, 'getQuestionTypes']);
        Route::delete('/tests/{id}', [TestController::class, 'destroy']);
        Route::post('/essay-reviews', [EssayReviewController::class, 'store']);
        // Questions management (Admin only)
        Route::apiResource('questions', QuestionController::class)->only(['store','update','destroy']);
        Route::get('/admin-only', fn () => response()->json(['message' => 'Welcome admin!']));
        Route::apiResource('users', UserController::class)->except(['show']);
        Route::get('test-management/{id}', [TestManagementController::class, 'data']);
        Route::get('/questions-manage/{test}', [QuestionController::class, 'index']);
        Route::post('/questions-manage/{test}', [QuestionController::class, 'store']);
        Route::put('/questions-manage/{test}', [QuestionController::class, 'update']);
        Route::delete('/questions-manage/{test}', [QuestionController::class, 'destroy']);
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
        Route::get('/statistics', [StatisticsController::class, 'data']);
        Route::get('/testManagement', [TestManagementController::class, 'data']);
    });
});
