<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rating extends Model
{
    /**
     * @param  array<string, mixed>  $fillable
     */
    protected $fillable = ['reviewer_id', 'target_id', 'target_type', 'overall_rating', 'comment'];

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_id');
    }

    public function ratingItems(): HasMany
    {
        return $this->hasMany(RatingItem::class);
    }

    public function targetType(): BelongsTo
    {
        return $this->belongsTo(VotingCategory::class, 'target_type_id');
    }
}
