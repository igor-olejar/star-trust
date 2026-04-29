<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\RegisterUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request): View
    {
        $selectedType = $request->query('type', 'artist');

        return view('auth.register', compact('selectedType'));
    }

    public function register(RegisterRequest $request, RegisterUserAction $registerUserAction): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $user = $registerUserAction->execute($validated);
            event(new Registered($user));
            Auth::login($user);

            return redirect('dashboard')->with('message', 'Registration successful! Please check your email to verify your account.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }
}
