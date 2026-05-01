<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reviewer_hash',
        'target_id',
        'comment',
    ];

    public function target()
    {
        return $this->belongsTo(User::class, 'target_id');
    }
}
