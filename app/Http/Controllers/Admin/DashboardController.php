<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\UserStatus;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        $pendingCount = User::where('status', UserStatus::VERIFIED)->count();
        $pendingUsers = User::where('status', UserStatus::VERIFIED)
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        $activeCount = User::where('status', UserStatus::ACTIVE)->count();

        return view('admin.dashboard', [
            'admin' => Auth::guard('admin')->user(),
            'pendingCount' => $pendingCount,
            'pendingUsers' => $pendingUsers,
            'activeCount' => $activeCount,
        ]);
    }
}

