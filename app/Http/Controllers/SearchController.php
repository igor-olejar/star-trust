<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\UserStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Laravel\Scout\Builder;

class SearchController extends Controller
{
    /**
     * Display the main search results page.
     */
    public function index(Request $request): View
    {
        $query = $request->input('q');

        if (!$request->filled('q')) {
            return view('search.index', [
                'results' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'query' => null,
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
     */
    protected function applySearchLogic(?string $searchTerm): Builder
    {
        // Use 'use' to bring the current user into the closure scope
        $authUser = Auth::user();

        return User::search($searchTerm, function ($meilisearch, $query, $options) use ($authUser) {
            // Start with basic filters
            $filters = [
                "status = " . UserStatus::ACTIVE->value,
                "id != " . $authUser->id,
                "user_type_id != " . $authUser->user_type_id->value,
            ];

            $typeMap = [
                'venue'    => 1, 'venues'    => 1,
                'artist'   => 2, 'artists'   => 2,
                'promoter' => 3, 'promoters' => 3,
            ];

            $lowerQuery = strtolower(trim($query));

            // If searching a type that ISN'T the user's own type, apply the filter
            if (array_key_exists($lowerQuery, $typeMap)) {
                $targetTypeId = $typeMap[$lowerQuery];
                if ($authUser->user_type_id->value !== $targetTypeId) {
                    $filters[] = "user_type_id = $targetTypeId";
                }
            }

            $options['filter'] = implode(' AND ', $filters);

            return $meilisearch->search($query, $options);
        });
    }
}
