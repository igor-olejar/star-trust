<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
route::post('/register', [RegisterController::class, 'register']);

Route::middleware(['auth', 'verified'])->group(function () {
    //Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    //Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');
});
