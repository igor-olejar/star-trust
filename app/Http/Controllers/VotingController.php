<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class VotingController extends Controller
{
    public function show(Request $request, int $target_user_id)
    {
        $targetUser = User::findOrFail($target_user_id);

        return view('voting.show', ['targetUser' => $targetUser]);
    }
}
