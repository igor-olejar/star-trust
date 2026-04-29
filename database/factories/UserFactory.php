<?php

namespace Database\Factories;

use App\Models\User;
use App\UserType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'user_type_id' => fake()->randomElement(UserType::cases()),
            'status' => fake()->randomElement(['verified', 'pending', 'blocked']),
            'city' => fake()->city(),
            'country_code' => fake()->countryCode(),
            'socials' => [
                'twitter' => fake()->boolean() ? fake()->userName() : null,
                'facebook' => fake()->boolean() ? fake()->userName() : null,
                'instagram' => fake()->boolean() ? fake()->userName() : null,
            ],
            'website' => fake()->boolean() ? fake()->url() : null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
