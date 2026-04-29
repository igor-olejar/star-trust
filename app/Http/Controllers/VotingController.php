<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\RatingItem;
use App\Models\User;
use App\Models\VotingCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VotingController extends Controller
{
    public function show(Request $request, int $targetUserId): View
    {
        $targetUser = User::findOrFail($targetUserId);
        $categories = VotingCategory::where('target_type_id', $targetUser->user_type_id->value)
            ->get();

        return view('voting.show', compact('targetUser', 'categories'));
    }

    public function saveRating(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'target_user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:voting_categories,id',
            'stars' => 'required|integer|min:1|max:5',
        ]);

        $targetUser = User::findOrFail($validated['target_user_id']);
        /** @var User $targetUser */
        $authUser = Auth::guard('web')->user();
        /** @var User $authUser */
        $rating = Rating::updateOrCreate(
            [
                'reviewer_id' => $authUser->id,
                'target_id' => $targetUser->id,
            ],
            [
                'target_type_id' => $targetUser->user_type_id->value,
                'overall_rating' => $validated['stars'], // Temporary initial value
            ]
        );

        $ratingItem = $rating->ratingItems()->where('voting_category_id', $validated['category_id'])->first();

        if ($ratingItem instanceof RatingItem) {
            $ratingItem->update([
                'score' => $validated['stars'],
                'number_of_votes' => $ratingItem->number_of_votes + 1, // Increment count
            ]);
        } else {
            $ratingItem = $rating->ratingItems()->create([
                'voting_category_id' => $validated['category_id'],
                'score' => $validated['stars'],
                'number_of_votes' => 1,
            ]);
        }

        // 3. Recalculate the true weighted average for the main Rating record
        $items = $rating->ratingItems()->with('votingCategory')->get();

        $totalWeightedScore = 0;
        foreach ($items as $item) {
            // Multiply the score by the category weight (e.g., 0.3) from the DB
            assert($item instanceof RatingItem);
            /** @var VotingCategory $votingCategory */
            $votingCategory = $item->votingCategory;
            $totalWeightedScore += ($item->score * $votingCategory->weight);
        }

        $rating->update(['overall_rating' => $totalWeightedScore]);

        return response()->json([
            'success' => true,
            'new_average' => round($targetUser->averageScore(), 1),
        ]);
    }
}
