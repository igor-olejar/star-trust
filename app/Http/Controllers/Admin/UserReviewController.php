<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\UserStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class UserReviewController extends Controller
{
    public function index(): View
    {
        // Email verification is performed by the user; admin review starts once the user is VERIFIED.
        $users = User::where('status', UserStatus::VERIFIED)->get();

        return view('admin.user-review.index', compact('users'));
    }

    public function show(User $user): View
    {
        return view('admin.user-review.show', [
            'user' => $user,
        ]);
    }

    public function activate(Request $request, User $user): RedirectResponse
    {
        if ($user->status !== UserStatus::VERIFIED) {
            return redirect()
                ->route('admin.users.review.show', $user)
                ->with('error', 'Only verified users can be activated.');
        }

        $user->update(['status' => UserStatus::ACTIVE]);

        return redirect()->route('admin.users.review')->with('success', 'User approved successfully');
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        if ($user->status !== UserStatus::VERIFIED) {
            return redirect()
                ->route('admin.users.review.show', $user)
                ->with('error', 'Only verified users can be rejected.');
        }

        $user->update(['status' => UserStatus::REJECTED]);

        return redirect()->route('admin.users.review')->with('success', 'User rejected successfully');
    }

    public function block(Request $request, User $user): RedirectResponse
    {
        if (!in_array($user->status, [UserStatus::VERIFIED, UserStatus::ACTIVE], true)) {
            return redirect()
                ->route('admin.users.review.show', $user)
                ->with('error', 'Only verified or active users can be blocked.');
        }

        $user->update(['status' => UserStatus::BLOCKED]);

        return redirect()->route('admin.users.review')->with('success', 'User blocked successfully');
    }
}

