<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->get('/admin/statistics/data', [StatisticsController::class, 'data'])
    ->name('admin.statistics.data');
// Route dùng cho mọi trang frontend của Vue
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
