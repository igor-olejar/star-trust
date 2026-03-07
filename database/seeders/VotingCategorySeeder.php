<?php

namespace Database\Seeders;

use App\Models\VotingCategory;
use App\UserType;
use App\VotingCategory as AppVotingCategory;
use Illuminate\Database\Seeder;

class VotingCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => AppVotingCategory::PUNCTUALITY->label(), 'target_type_id' => UserType::ARTIST->value, 'weight' => 0.3],
            ['name' => AppVotingCategory::PROFESSIONALISM->label(), 'target_type_id' => UserType::ARTIST->value, 'weight' => 0.3],
            ['name' => AppVotingCategory::STAGE_MANNER->label(), 'target_type_id' => UserType::ARTIST->value, 'weight' => 0.4],
            ['name' => AppVotingCategory::TECH_AND_SOUND->label(), 'target_type_id' => UserType::VENUE->value, 'weight' => 0.4],
            ['name' => AppVotingCategory::COMMUNICATION->label(), 'target_type_id' => UserType::VENUE->value, 'weight' => 0.3],
            ['name' => AppVotingCategory::SAFETY_SECURITY->label(), 'target_type_id' => UserType::VENUE->value, 'weight' => 0.3],
            ['name' => AppVotingCategory::PROMO_EFFORTS->label(), 'target_type_id' => UserType::PROMOTER->value, 'weight' => 0.4],
            ['name' => AppVotingCategory::ORGANISATION->label(), 'target_type_id' => UserType::PROMOTER->value, 'weight' => 0.3],
            ['name' => AppVotingCategory::COMMUNICATION->label(), 'target_type_id' => UserType::PROMOTER->value, 'weight' => 0.3],
        ];

        foreach ($data as $item) {
            VotingCategory::firstOrCreate(
                [
                    'name' => $item['name'],
                    'target_type_id' => $item['target_type_id'],
                ],
                [
                    'weight' => $item['weight'],
                ]
            );
        }
    }
}
