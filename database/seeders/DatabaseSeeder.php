<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\User;
use App\Models\Location;
use App\Models\DistributionGroup;
use App\Models\DistributionGroupUser;
use App\Models\EquipmentIssue;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(200)->create();
        Asset::factory()->count(100)->create();
        Location::factory()->count(6)->create();
        EquipmentIssue::factory()->count(6)->create();
        DistributionGroup::factory()->count(6)->create();

        $distributionGroups = DistributionGroup::all();
        $users = User::all();

        foreach ($distributionGroups as $distributionGroup) {
            $numUsers = rand(2, 3); // Random number of users between 1 and 5

            $usersToAssign = $users->random($numUsers);
            foreach ($usersToAssign as $user) {
                DistributionGroupUser::factory()->create([
                    'distribution_group_id' => $distributionGroup->id,
                    'user_id' => $user->id,
                ]);
            }
        }

    }
}
