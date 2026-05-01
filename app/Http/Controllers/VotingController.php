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
    /**
     * Display the voting page for a specific user.
     */
    public function show(Request $request, int $targetUserId): View
    {
        $targetUser = User::findOrFail($targetUserId);
        
        $reviewerHash = hash_hmac('sha256', Auth::id(), config('app.key'));

        $categories = VotingCategory::where('target_type_id', $targetUser->user_type_id->value)
            ->get();

        // Fetch the single rating record for this anonymous reviewer
        $ratingRecord = Rating::where('reviewer_id', $reviewerHash)
            ->where('target_id', $targetUserId)
            ->first();

        // Prepare simple arrays for Alpine.js initialization in the view
        $existingScores = [];
        $existingVotes = [];

        if ($ratingRecord) {
            foreach ($ratingRecord->ratingItems as $item) {
                $existingScores[$item->voting_category_id] = $item->score;
                $existingVotes[$item->voting_category_id] = $item->number_of_votes;
            }
        }

        return view('voting.show', compact(
            'targetUser', 
            'categories', 
            'existingScores', 
            'existingVotes'
        ));
    }

    /**
     * Save or update a rating for a specific category.
     */
    public function saveRating(VotingRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $targetUser = User::findOrFail($validated['target_user_id']);
        $reviewerHash = hash_hmac('sha256', Auth::id(), config('app.key'));
        // 1. Get or Create the parent Rating record
        $rating = Rating::updateOrCreate(
            [
                'reviewer_id' => $reviewerHash,
                'target_id' => $targetUser->id,
            ],
            [
                'target_type_id' => $targetUser->user_type_id->value,
                'overall_rating' => $validated['stars'], // Initial temporary value
            ]
        );

        // 2. Manage the specific RatingItem
        $ratingItem = $rating->ratingItems()
            ->where('voting_category_id', $validated['category_id'])
            ->first();

        if ($ratingItem instanceof RatingItem) {
            // Backend Guard: Ensure the 3-vote limit is respected
            if ($ratingItem->number_of_votes >= 3) {
                return response()->json(['error' => 'Vote limit reached for this category'], 403);
            }

            $ratingItem->update([
                'score' => $validated['stars'],
                'number_of_votes' => $ratingItem->number_of_votes + 1, // Increment count
            ]);
        } else {
            // First time voting in this category
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
            /** @var RatingItem $item */
            /** @var VotingCategory $votingCategory */
            $votingCategory = $item->votingCategory;
            
            // Apply the weight defined in the category (e.g., 0.3) to the star score
            $totalWeightedScore += ($item->score * $votingCategory->weight);
        }

        // Update the parent record with the final calculated score
        $rating->update(['overall_rating' => $totalWeightedScore]);

        return response()->json([
            'success' => true,
            'new_average' => round($targetUser->averageScore(), 1),
        ]);
    }
}
