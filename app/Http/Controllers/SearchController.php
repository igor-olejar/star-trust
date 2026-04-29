<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\UserStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Scout\Builder;

class SearchController extends Controller
{
    /**
     * Display the main search results page.
     */
    public function index(Request $request): View
    {
        $query = $request->input('q');

        if (! $request->filled('q')) {
            return view('search.index', [
                'results' => new LengthAwarePaginator([], 0, 15),
                'query' => null,
                'message' => 'Please enter a search term',
            ]);
        }

        $results = $this->applySearchLogic($query)->paginate(15);

        return view('search.index', [
            'results' => $results,
            'query' => $query,
        ]);
    }

    /**
     * Provide JSON suggestions for the Alpine.js dropdown.
     */
    public function searchSuggestions(Request $request): JsonResponse
    {
        $query = $request->input('q');

        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $results = $this->applySearchLogic($query)
            ->take(5)
            ->get();

        return response()->json($results);
    }

    /**
     * Centralized logic for filtering and searching.
     * This keeps the logic DRY across index and suggestions.
     * @return Builder
     */
    protected function applySearchLogic(?string $searchTerm): Builder
    {
        // Use 'use' to bring the current user into the closure scope
        $authUser = Auth::guard('web')->user();

        return User::search($searchTerm, function ($meilisearch, $query, $options) use ($authUser) {
            $filters = [
                'status = '.UserStatus::ACTIVE->value,
                'id != '.$authUser->id,
                'user_type_id != '.$authUser->user_type_id->value,
            ];

            $typeMap = [
                'venue' => 1, 'venues' => 1,
                'artist' => 2, 'artists' => 2,
                'promoter' => 3, 'promoters' => 3,
            ];

            $lowerQuery = strtolower(trim($query));

            if (array_key_exists($lowerQuery, $typeMap)) {
                $targetTypeId = $typeMap[$lowerQuery];
                $filters[] = "user_type_id = $targetTypeId";
            }

            $options['filter'] = implode(' AND ', $filters);

            return $meilisearch->search($query, $options);
        });
    }
}
