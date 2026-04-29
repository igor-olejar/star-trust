<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $rating_id
 * @property int $voting_category_id
 * @property int $score
 * @property int $number_of_votes
 */
class RatingItem extends Model
{
    protected $fillable = [
        'rating_id',
        'voting_category_id',
        'score',
        'number_of_votes',
    ];

    public function votingCategory(): BelongsTo
    {
        return $this->belongsTo(VotingCategory::class);
    }
}
