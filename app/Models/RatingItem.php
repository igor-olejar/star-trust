<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingItem extends Model
{
    public function votingCategory()
    {
        return $this->belongsTo(VotingCategory::class);
    }
}
