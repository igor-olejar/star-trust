<?php

namespace App\Http\Middleware;

use App\UserStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAllowedToVote
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $targetUserId = $request->input('target_user_id');
        $currentUser = Auth::guard('web')->user();

        if (!$currentUser) {
            return redirect()->route('login')->with('error', 'You must be logged in to vote.');
        }

        if ($currentUser->status !== UserStatus::ACTIVE) {
            return $this->deny($request);
        }

        if ((int) $currentUser->id === (int) $targetUserId) {
            return $this->deny($request);
        }

        return $next($request);
    }

    protected function deny(Request $request): Response
    {
        return redirect()->back()->with('error', 'You are not allowed to vote on this person.');
    }
}
