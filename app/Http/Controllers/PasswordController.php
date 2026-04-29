<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class PasswordController extends Controller
{
    public function reset(ResetPasswordRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return back()->withErrors(['email' => 'No user found with this email address.']);
        }

        $user->password = bcrypt($validated['password']);
        $user->save();

        return redirect()->route('login')->with('message', 'Password reset successful! You can now log in with your new password.');
    }
}
