<?php

namespace Database\Seeders;

use App\Models\VotingCategory;
use Illuminate\Database\Seeder;

class VotingCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Artist categories
            ['name' => 'Punctuality', 'target_type_id' => 2, 'weight' => 0.3],
            ['name' => 'Professionalism', 'target_type_id' => 2, 'weight' => 0.3],
            ['name' => 'Stage Manner', 'target_type_id' => 2, 'weight' => 0.4],
            // Venue categories
            ['name' => 'Tech and Sound', 'target_type_id' => 1, 'weight' => 0.4],
            ['name' => 'Communication', 'target_type_id' => 1, 'weight' => 0.3],
            ['name' => 'Safety/Security', 'target_type_id' => 1, 'weight' => 0.3],
            // Promoter categories
            ['name' => 'Promo efforts', 'target_type_id' => 3, 'weight' => 0.4],
            ['name' => 'Communication', 'target_type_id' => 3, 'weight' => 0.3],
            ['name' => 'Organisation', 'target_type_id' => 3, 'weight' => 0.3],
        ];

        foreach ($categories as $category) {
            VotingCategory::create($category);
        }
    }
}
