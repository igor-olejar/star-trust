<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\UserStatus;

use Illuminate\Contracts\View\View;

class UserListController extends Controller
{
    public function pending(): View
    {
        $users = User::where('status', UserStatus::PENDING)->get();

        return view('admin.users.list.pending', compact('users'));
    }

    public function active(): View
    {
        $users = User::where('status', UserStatus::ACTIVE)->get();

        return view('admin.users.list.active', compact('users'));
    }

    public function blocked(): View
    {
        $users = User::where('status', UserStatus::BLOCKED)->get();

        return view('admin.users.list.blocked', compact('users'));
    }

    public function rejected(): View
    {
        $users = User::where('status', UserStatus::REJECTED)->get();

        return view('admin.users.list.rejected', compact('users'));
    }
}
