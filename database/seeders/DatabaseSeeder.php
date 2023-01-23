<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\User;
use App\Models\Location;
use App\Models\DistributionGroup;
use App\Models\DistributionGroupUser;
use App\Models\EquipmentIssue;
use App\Models\Role;
use App\Models\Loan;
use App\Models\Setup;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //Make admin user
        $userAdmin = User::factory()->count(1)->withPasswordSet()->create()->first();
        Role::factory()->count(1)->withUser($userAdmin)->create();

        User::factory()->count(100)->create();
        Asset::factory()->count(100)->create();
        Location::factory()->count(6)->create();
        EquipmentIssue::factory()->count(6)->create();
        DistributionGroup::factory()->count(6)->create();

        $distributionGroups = DistributionGroup::all();
        $users = User::all();
        $locations = Location::all()->all();

        /**
         * Each user will have two loans, the first is a real loan, the second
         * is associated with a setup, with a random location.
         */
        $assetIndex = 0;
        foreach($users as $user){
            Role::factory()->count(1)->withUser($user)->create();
            $loan = Loan::factory()->count(1)->withUser($user)->withCreator($userAdmin)->create()->first();
            $loan->assets()->attach(Asset::skip($assetIndex)->first());
            $assetIndex++;

            $setupLoan = Loan::factory()->count(1)->withUser($user)->withCreator($userAdmin)->withStatusId(3)->create()->first();
            Setup::factory()->count(1)->withLoan($setupLoan)->withLocation($locations[array_rand($locations)])->create();
        }

        foreach ($distributionGroups as $distributionGroup) {
            $numUsers = rand(2, 3);

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
