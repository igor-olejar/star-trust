<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'user_genre');
    }

    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class);
    }

    // Ratings this user has written
    public function ratingsGiven(): HasMany
    {
        return $this->hasMany(Rating::class, 'reviewer_id');
    }

    // Ratings other people have left for this user
    public function ratingsReceived(): HasMany
    {
        return $this->hasMany(Rating::class, 'target_id');
    }

    public function averageScore(): mixed
    {
        return $this->ratingsReceived()->avg('overall_rating') ?: 0;
    }
}
