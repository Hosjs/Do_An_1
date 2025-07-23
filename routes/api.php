<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TestController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::middleware('role:Admin')->post('/tests/generate', [TestController::class, 'generateTest']);
    Route::middleware('role:Admin')->get('/subjects', [TestController::class, 'getSubjects']);
    Route::middleware('role:Admin')->get('/question-types', [TestController::class, 'getQuestionTypes']);
    Route::middleware('role:Admin')->delete('/tests/{id}', [TestController::class, 'destroy']);

    Route::middleware('role:Admin')->get('/tests', [TestController::class, 'index']);
    Route::middleware('role:Admin')->get('/tests/{id}', [TestController::class, 'show']);
    Route::get('/tests/{id}', [TestController::class, 'show']);


    Route::middleware('role:Admin')->get('/admin-only', function () {
        return response()->json(['message' => 'Welcome admin!']);
    });

    Route::middleware('role:Student')->get('/student-only', function () {
        return response()->json(['message' => 'Welcome student!']);
    });
});

