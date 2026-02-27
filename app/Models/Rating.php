<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rating extends Model
{
    // The user who wrote the review
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // The user being reviewed (Artist, Venue, or Promoter)
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
        return $this->belongsTo(UserType::class, 'target_type_id');
    }
}
