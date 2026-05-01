<?php

namespace App\Http\Controllers;

use App\Http\Requests\VotingRequest;
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
        $reviewerHash = hash_hmac('sha256', Auth::id(), config('app.key'));
        $categories = VotingCategory::where('target_type_id', $targetUser->user_type_id->value)
            ->get();

        $existingRatings = $targetUser->ratingsReceived()
            ->where('reviewer_id', $reviewerHash)
            ->first()
            ?->ratingItems
            ->pluck('number_of_votes', 'voting_category_id') ?? collect();

        return view('voting.show', compact('targetUser', 'categories', 'existingRatings'));
    }

    public function saveRating(VotingRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $targetUser = User::findOrFail($validated['target_user_id']);
        /** @var User $targetUser */
        $authUser = Auth::guard('web')->user();
        /** @var User $authUser */

        $reviewerHash = hash_hmac('sha256', $authUser->id, config('app.key'));

        $rating = Rating::updateOrCreate(
            [
                'reviewer_id' => $reviewerHash,
                'target_id' => $targetUser->id,
            ],
            [
                'target_type_id' => $targetUser->user_type_id->value,
                'overall_rating' => $validated['stars'],
            ]
        );

        $ratingItem = $rating->ratingItems()
            ->where('voting_category_id', $validated['category_id'])
            ->first();

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
