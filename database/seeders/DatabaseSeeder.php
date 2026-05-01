<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserTypeSeeder::class,
            GenreSeeder::class,
            VotingCategorySeeder::class,
            CommentSeeder::class,
        ]);

        $users = User::factory(30)->create();

        $genres = Genre::all();
        $users->each(function (User $user) use ($genres): void {
            $user->genres()->attach(
                $genres->random(2)->pluck('id')
            );
        });

        $this->call([RatingSeeder::class]);
    }
}
