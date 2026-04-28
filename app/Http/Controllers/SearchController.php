<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\UserStatus;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        $results = User::search($query)
            ->where('status', UserStatus::ACTIVE->value)
            ->paginate(15);

        return view('search.index', [
            'results' => $results,
            'query' => $query,
        ]);
    }
}
