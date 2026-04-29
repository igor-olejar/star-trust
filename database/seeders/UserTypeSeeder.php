<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userTypes = ['Venue', 'Artist', 'Promoter'];

        foreach ($userTypes as $userType) {
            UserType::firstOrCreate(['name' => $userType]);
        }

    }
}
