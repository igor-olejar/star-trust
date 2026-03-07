<?php

namespace Database\Seeders;

use App\Models\Rating;
use App\Models\User;
use App\Models\VotingCategory;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if (VotingCategory::count() === 0) {
            $this->call(VotingCategorySeeder::class);
        }

        for ($i = 0; $i < 10; $i++) {
            $reviewer = $users->random();
            $target = $users->where('id', '!=', $reviewer->id)->random();

            $ratings = Rating::create([
                'reviewer_id' => $reviewer->id,
                'target_id' => $target->id,
                'target_type_id' => $target->user_type_id->value,
                'overall_rating' => 0,
            ]);

            $votingCatetories = VotingCategory::where('target_type_id', $target->user_type_id)->get();

            foreach ($votingCatetories as $index => $category) {
                $voteCount = 3 - $index; // Decreasing vote count for each category

                for ($v = 1; $v <= $voteCount; $v++) {
                    $ratings->ratingItems()->create([
                        'voting_category_id' => $category->id,
                        'score' => rand(3, 5),
                        'number_of_votes' => $v,
                    ]);
                }
            }

            $ratings->update(['overall_rating' => $ratings->ratingItems()->avg('score')]);
        }
    }
}
