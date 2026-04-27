<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();
        $validated['socials'] = [
            'instagram' => $validated['instagram'] ?? null,
            'facebook' => $validated['facebook'] ?? null,
        ];

        $user->update($validated);

        return redirect()->route('dashboard')->with('message', 'Profile updated!');
    }
}
