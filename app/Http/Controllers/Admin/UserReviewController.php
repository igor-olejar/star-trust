<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\UserStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UserReviewController extends Controller
{
    public function index(): View
    {
        $users = User::where('status', UserStatus::PENDING)->get();

        return view('admin.user-review.index', compact('users'));
    }

    public function show(User $user): View
    {
        return view('admin.user-review.show', [
            'user' => $user,
        ]);
    }

    public function verify(Request $request, User $user): RedirectResponse
    {
        $user->update(['status' => UserStatus::VERIFIED]);

        return redirect()->route('admin.users.review')->with('success', 'User verified successfully');
    }

    public function activate(Request $request, User $user): RedirectResponse
    {
        $user->update(['status' => UserStatus::ACTIVE]);

        return redirect()->route('admin.users.review')->with('success', 'User approved successfully');
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        $user->update(['status' => UserStatus::REJECTED]);

        return redirect()->route('admin.users.review')->with('success', 'User rejected successfully');
    }

    public function block(Request $request, User $user): RedirectResponse
    {
        $user->update(['status' => UserStatus::BLOCKED]);

        return redirect()->route('admin.users.review')->with('success', 'User blocked successfully');
    }
}

