<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserStatusChange;
use App\UserStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        if (! in_array($user->status, [UserStatus::PENDING, UserStatus::VERIFIED, UserStatus::BLOCKED], true)) {
            return redirect()
                ->route('admin.users.review.show', $user)
                ->with('error', 'Only pending, verified, or blocked users can be activated.');
        }

        DB::transaction(function () use ($user) {
            $from = $user->status;
            $user->update(['status' => UserStatus::ACTIVE]);

            UserStatusChange::create([
                'user_id' => $user->id,
                'admin_id' => auth('admin')->id(),
                'from_status' => $from->value,
                'to_status' => UserStatus::ACTIVE->value,
            ]);
        });

        return redirect()->route('admin.users.review')->with('success', 'User approved successfully');
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        if (! in_array($user->status, [UserStatus::PENDING, UserStatus::VERIFIED, UserStatus::ACTIVE], true)) {
            return redirect()
                ->route('admin.users.review.show', $user)
                ->with('error', 'Only pending, verified, or active users can be rejected.');
        }

        DB::transaction(function () use ($user) {
            $from = $user->status;
            $user->update(['status' => UserStatus::REJECTED]);

            UserStatusChange::create([
                'user_id' => $user->id,
                'admin_id' => auth('admin')->id(),
                'from_status' => $from->value,
                'to_status' => UserStatus::REJECTED->value,
            ]);
        });

        return redirect()->route('admin.users.review')->with('success', 'User rejected successfully');
    }

    public function block(Request $request, User $user): RedirectResponse
    {
        if (! in_array($user->status, [UserStatus::VERIFIED, UserStatus::ACTIVE], true)) {
            return redirect()
                ->route('admin.users.review.show', $user)
                ->with('error', 'Only verified or active users can be blocked.');
        }

        DB::transaction(function () use ($user) {
            $from = $user->status;
            $user->update(['status' => UserStatus::BLOCKED]);

            UserStatusChange::create([
                'user_id' => $user->id,
                'admin_id' => auth('admin')->id(),
                'from_status' => $from->value,
                'to_status' => UserStatus::BLOCKED->value,
            ]);
        });

        return redirect()->route('admin.users.review')->with('success', 'User blocked successfully');
    }
}
