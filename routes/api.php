<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\EssayReviewController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::middleware('role:Admin')->group(function () {
        Route::post('/tests/generate', [TestController::class, 'generateTest']);
        Route::get('/subjects', [TestController::class, 'getSubjects']);
        Route::get('/question-types', [TestController::class, 'getQuestionTypes']);
        Route::delete('/tests/{id}', [TestController::class, 'destroy']);
        Route::get('/tests', [TestController::class, 'index']);
        Route::post('/essay-reviews', [EssayReviewController::class, 'store']);
        Route::post('/tests/storeUserAnswers', [TestController::class, 'storeUserAnswers']);
    });

    Route::get('/tests/{id}', [TestController::class, 'show']);


    Route::middleware('role:Admin')->get('/admin-only', function () {
        return response()->json(['message' => 'Welcome admin!']);
    });

    Route::middleware('role:Student')->get('/student-only', function () {
        return response()->json(['message' => 'Welcome student!']);
    });
});
