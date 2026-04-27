<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
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
        'user_type_id',
        'status',
        'city',
        'country_code',
        'socials',
        'website',
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
            'user_type_id' => \App\UserType::class,
            'socials' => 'array',
            'status' => \App\UserStatus::class,
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

    public function statusChanges(): HasMany
    {
        return $this->hasMany(UserStatusChange::class);
    }

    public function averageScore(): mixed
    {
        return $this->ratingsReceived()->avg('overall_rating') ?: 0;
    }

    public function getCountryNameAttribute(): string
    {
        $countries = [
            'GB' => 'United Kingdom',
        ];

        return $countries[$this->country_code] ?? $this->country_code ?? 'Not Set';
    }
}
