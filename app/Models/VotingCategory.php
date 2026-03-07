<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VotingCategory extends Model
{
    protected $fillable = ['name'];

    public function ratingItems(): HasMany
    {
        return $this->hasMany(RatingItem::class);
    }

    protected function casts(): array
    {
        return [
            'id' => VotingCategory::class,
        ];
    }
}
