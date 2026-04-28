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

        if (!$request->filled('q')) {
            return view('search.index', [
                'results' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'query' => null,
                'message' => 'Please enter a search term'
            ]);
        }

        $results = User::search($query, function ($meilisearch, $query, $options) {
            $filters = ["status = " . UserStatus::ACTIVE->value];

            $typeMap = [
                'venue'    => 1,
                'artist'   => 2,
                'promoter' => 3,
            ];

            $lowerQuery = str_replace('s', '', strtolower(trim($query)));

            if (array_key_exists($lowerQuery, $typeMap)) {
                $filters[] = "user_type_id = " . $typeMap[$lowerQuery];
            }

            $options['filter'] = implode(' AND ', $filters);
            return $meilisearch->search($query, $options);
        })->paginate(15);

        return view('search.index', [
            'results' => $results,
            'query' => $query,
        ]);
    }
}
