<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reviewer_hash' => bin2hex(random_bytes(32)),
            'target_id' => User::factory(),
            'comment' => $this->faker->realText(150),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
