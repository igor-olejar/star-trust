<?php

use App\Actions\Auth\ChangeUserStatusAction;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProfileController;
use App\UserStatus;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
route::post('/register', [RegisterController::class, 'register']);

// 1. The "Notice" page (tells user to check their email)
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 2. The "Verify" handler (the link in the email points here)
Route::get('/email/verify/{id}/{hash}', function (
    EmailVerificationRequest $request,
    ChangeUserStatusAction $changeUserStatusAction,
): View {
    $request->fulfill();
    $changeUserStatusAction->execute($request->user(), UserStatus::VERIFIED);

    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    return view('auth.email-verified');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 3. The "Resend" handler
Route::post('/email/verification-notification', function (Request $request): RedirectResponse {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

Route::get('/logout', function (): RedirectResponse {
    Auth::logout();
    return redirect()->route('landing');
})->name('logout');

Route::get('/password/reset', function (Request $request): View {
    return view('auth.passwords.reset', ['request' => $request]);
})->name('password.request');

Route::post('/password/reset', [PasswordController::class, 'reset'])->name('password.reset');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    //Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    //Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');
});
