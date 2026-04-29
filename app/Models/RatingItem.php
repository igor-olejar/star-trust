<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RatingItem extends Model
{
    public function votingCategory(): BelongsTo
    {
        return $this->belongsTo(VotingCategory::class);
    }
}
