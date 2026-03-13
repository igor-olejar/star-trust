<?php

use App\Actions\Auth\ChangeUserStatusAction;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LandingController;
use App\UserStatus;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
route::post('/register', [RegisterController::class, 'register']);

// 1. The "Notice" page (tells user to check their email)
//Route::get('/email/verify', function () {
//    return view('auth.verify-email');
//})->middleware('auth')->name('verification.notice');

// 2. The "Verify" handler (the link in the email points here)
Route::get('/email/verify/{id}/{hash}', function (
    EmailVerificationRequest $request,
    ChangeUserStatusAction $changeUserStatusAction,
): RedirectResponse {
    $request->fulfill();
    $changeUserStatusAction->execute($request->user(), UserStatus::VERIFIED);
    return redirect('/dashboard')->with('message', 'Account verified successfully!');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 3. The "Resend" handler
//Route::post('/email/verification-notification', function (Request $request): RedirectResponse {
//    $request->user()->sendEmailVerificationNotification();
//    return back()->with('message', 'Verification link sent!');
//})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

Route::get('/logout', function (): RedirectResponse {
    Auth::logout();
    return redirect()->route('landing');
})->name('logout');

Route::get('/password/reset', function (): View {
    return view('auth.passwords.email');
})->name('password.request');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function (): View {
        return view('dashboard');
    })->name('dashboard');
    //Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    //Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');
});
