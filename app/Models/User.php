<?php

namespace App\Models;

use App\UserStatus;
use App\UserType;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Scout\Searchable;
use Symfony\Component\Intl\Countries;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property UserType $user_type_id
 * @property UserStatus $status
 * @property string|null $city
 * @property string|null $country_code
 * @property array<string, string|null>|null $socials
 * @property string|null $website
 * @property string|null $remember_token
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;
    use Searchable;

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
            'user_type_id' => UserType::class,
            'socials' => 'array',
            'status' => UserStatus::class,
        ];
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'user_genre');
    }

    public function userType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\UserType::class);
    }

    public function ratingsGiven(): HasMany
    {
        return $this->hasMany(Rating::class, 'reviewer_id');
    }

    public function ratingsReceived(): HasMany
    {
        return $this->hasMany(Rating::class, 'target_id');
    }

    public function statusChanges(): HasMany
    {
        return $this->hasMany(UserStatusChange::class);
    }

    public function averageScore(): float
    {
        return (float) $this->ratingsReceived()->avg('overall_rating') ?: 0;
    }

    public function totalRatingsCount(): int
    {
        return $this->ratingsReceived()->count();
    }

    public function getCountryNameAttribute(): string
    {
        $countries = [
            'GB' => 'United Kingdom',
        ];

        if (! $this->country_code) {
            return 'Not Set';
        }

        return $countries[$this->country_code] ?? $this->country_code;
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        $fullCountryName = $this->country_code && Countries::exists($this->country_code)
        ? Countries::getName($this->country_code)
        : '';

        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'city' => $this->city,
            'country_code' => $this->country_code,
            'country_name' => $fullCountryName,
            'user_type_label' => $this->user_type_id->label(),
            'user_type_id' => (int) $this->user_type_id->value,
            'status' => (string) $this->status->value,
        ];
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    protected function makeAllSearchableUsing($query): Builder
    {
        return $query->where('status', UserStatus::ACTIVE->value);
    }
}
