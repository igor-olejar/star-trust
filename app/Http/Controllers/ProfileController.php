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
            'website' => $validated['website'] ?? null,
        ];

        dd($validated);

        $user->update($validated);

        return redirect()->route('dashboard')->with('message', 'Profile updated!');
    }
}
