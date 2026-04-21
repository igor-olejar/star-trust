<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'admin' => Auth::guard('admin')->user(),
        ]);
    }
}

