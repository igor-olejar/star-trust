<?php

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserListController;
use App\Http\Controllers\Admin\UserReviewController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
});

Route::middleware('auth:admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::get('/users/review', [UserReviewController::class, 'index'])->name('users.review');
    Route::get('/users/review/{user}', [UserReviewController::class, 'show'])->name('users.review.show');
    Route::post('/users/review/{user}/activate', [UserReviewController::class, 'activate'])->name('users.review.activate');
    Route::post('/users/review/{user}/reject', [UserReviewController::class, 'reject'])->name('users.review.reject');
    Route::post('/users/review/{user}/block', [UserReviewController::class, 'block'])->name('users.review.block');

    Route::get('/users/list/pending', [UserListController::class, 'pending'])->name('users.list.pending');
    Route::get('/users/list/active', [UserListController::class, 'active'])->name('users.list.active');
    Route::get('/users/list/blocked', [UserListController::class, 'blocked'])->name('users.list.blocked');
    Route::get('/users/list/rejected', [UserListController::class, 'rejected'])->name('users.list.rejected');
});
