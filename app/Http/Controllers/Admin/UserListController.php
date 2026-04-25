<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\UserStatus;

class UserListController extends Controller
{
    public function pending()
    {
        $users = User::where('status', UserStatus::PENDING)->get();
        return view('admin.users.list.pending', compact('users'));
    }

    public function active()
    {
        $users = User::where('status', UserStatus::ACTIVE)->get();
        return view('admin.users.list.active', compact('users'));
    }

    public function blocked()
    {
        $users = User::where('status', UserStatus::BLOCKED)->get();
        return view('admin.users.list.blocked', compact('users'));
    }

    public function rejected()
    {
        $users = User::where('status', UserStatus::REJECTED)->get();
        return view('admin.users.list.rejected', compact('users'));
    }
}
